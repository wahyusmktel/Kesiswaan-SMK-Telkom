<?php

namespace App\Services;

use App\Models\RoleMenuSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Throwable;

class RoleMenuService
{
    /**
     * Settings used by the browser to arrange the currently rendered navigation.
     * An empty payload deliberately leaves the original Blade navigation untouched.
     */
    public function settingsForActiveRole(): array
    {
        $roleName = session('active_role') ?: auth()->user()?->getRoleNames()->first();

        if (! $roleName || ! $this->tableIsReady()) {
            return [];
        }

        try {
            $role = Role::query()->where('name', $roleName)->first();

            return $role ? $this->settingsForRole($role) : [];
        } catch (Throwable) {
            return [];
        }
    }

    public function settingsForRole(Role $role): array
    {
        if (! $this->tableIsReady()) {
            return [];
        }

        return Cache::remember($this->cacheKey($role), now()->addMinutes(10), fn () => RoleMenuSetting::query()
            ->where('role_id', $role->getKey())
            ->orderBy('sort_order')
            ->get(['menu_key', 'is_visible', 'sort_order'])
            ->mapWithKeys(fn (RoleMenuSetting $setting) => [
                $setting->menu_key => [
                    'visible' => $setting->is_visible,
                    'order' => $setting->sort_order,
                ],
            ])->all());
    }

    /**
     * Render the real navigation as a representative user of the selected role,
     * then discover its top-level feature menus. This keeps the management page
     * in sync with conditional Blade menus without maintaining a second catalog.
     */
    public function discoverForRole(Role $role): array
    {
        $originalUser = Auth::user();
        $hadActiveRole = session()->has('active_role');
        $originalActiveRole = session('active_role');

        try {
            // Use a neutral user with only this role. A real multi-role account
            // could otherwise leak menus granted by one of its other roles.
            $representative = $this->syntheticUserFor($role);

            Auth::setUser($representative);
            session(['active_role' => $role->name]);

            $html = view('layouts.navigation')->render();
        } finally {
            Auth::setUser($originalUser);

            if ($hadActiveRole) {
                session(['active_role' => $originalActiveRole]);
            } else {
                session()->forget('active_role');
            }
        }

        return $this->extractMenus($html ?? '', $role);
    }

    public function clear(Role $role): void
    {
        Cache::forget($this->cacheKey($role));
    }

    public function menuKey(string $section, string $label, int $occurrence = 1): string
    {
        $base = Str::slug($section.' '.$label);

        return $occurrence > 1 ? $base.'-'.$occurrence : $base;
    }

    private function extractMenus(string $html, Role $role): array
    {
        preg_match_all(
            '/<div\b[^>]*class=["\'][^"\']*\bsection-title\b[^"\']*["\'][^>]*>(.*?)<\/div>|<li\b[^>]*>(.*?)<\/li>/is',
            $html,
            $tokens,
            PREG_SET_ORDER,
        );

        $section = 'Menu';
        $position = 0;
        $sectionPosition = 0;
        $occurrences = [];
        $menus = [];
        $settings = $this->settingsForRole($role);

        foreach ($tokens as $token) {
            if (($token[1] ?? '') !== '') {
                $section = $this->plainText($token[1]) ?: 'Menu';
                $sectionPosition++;

                continue;
            }

            $itemHtml = $token[2] ?? '';
            if (! preg_match('/<[^>]*class=["\'][^"\']*\bnav-text\b[^"\']*["\'][^>]*>(.*?)<\//is', $itemHtml, $labelMatch)) {
                continue;
            }

            $label = $this->plainText($labelMatch[1]);
            if ($label === '') {
                continue;
            }

            $base = Str::slug($section.' '.$label);
            $occurrences[$base] = ($occurrences[$base] ?? 0) + 1;
            $key = $this->menuKey($section, $label, $occurrences[$base]);
            $setting = $settings[$key] ?? null;

            $menus[] = [
                'key' => $key,
                'label' => $label,
                'section' => $section,
                'visible' => $setting['visible'] ?? true,
                'order' => $setting['order'] ?? $position,
                'default_order' => $position,
                'section_order' => $sectionPosition,
                'locked' => in_array($label, ['Profile', 'Keluar'], true)
                    || ($role->name === 'Super Admin' && $label === 'Pengaturan Menu'),
            ];
            $position++;
        }

        usort($menus, fn (array $a, array $b) => [$a['section_order'], $a['order'], $a['default_order']]
            <=> [$b['section_order'], $b['order'], $b['default_order']]);

        return array_values($menus);
    }

    private function syntheticUserFor(Role $role): User
    {
        $user = new User([
            'name' => 'Preview '.$role->name,
            'email' => 'preview-role-'.Str::slug($role->name).'@sisfo.invalid',
        ]);
        $user->forceFill(['id' => 0, 'email_verified_at' => now()]);
        $user->exists = true;
        $user->setRelation('roles', collect([$role->loadMissing('permissions')]));

        return $user;
    }

    private function plainText(string $html): string
    {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? '');
    }

    private function tableIsReady(): bool
    {
        try {
            return Schema::hasTable('role_menu_settings');
        } catch (Throwable) {
            return false;
        }
    }

    private function cacheKey(Role $role): string
    {
        return 'role-menu-settings:'.$role->getKey();
    }
}
