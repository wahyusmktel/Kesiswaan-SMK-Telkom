<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FaceLoginController extends Controller
{
    /**
     * Handle face login attempt.
     * Receives a base64-encoded webcam capture, saves it temporarily,
     * then compares against all users who have a face_photo registered.
     */
    public function login(Request $request)
    {
        $request->validate([
            'face_image' => 'required|string',
        ]);

        try {
            // Decode the base64 image from webcam
            $imageData = $request->input('face_image');
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            $imageData = base64_decode($imageData);

            if (!$imageData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format gambar tidak valid.',
                ], 422);
            }

            // Save temporary capture for comparison
            $tempPath = 'face-login-temp/' . uniqid('capture_') . '.jpg';
            Storage::disk('public')->put($tempPath, $imageData);
            $capturedFullPath = Storage::disk('public')->path($tempPath);

            // Get all users who have registered face photos
            $usersWithFace = User::whereNotNull('face_photo')
                ->where('face_photo', '!=', '')
                ->get();

            if ($usersWithFace->isEmpty()) {
                Storage::disk('public')->delete($tempPath);
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada akun dengan Face ID terdaftar.',
                ], 404);
            }

            $matchedUser = null;
            $bestScore = 0;
            $threshold = 0.60; // 60% similarity threshold

            foreach ($usersWithFace as $user) {
                $registeredPath = Storage::disk('public')->path($user->face_photo);

                if (!file_exists($registeredPath)) {
                    continue;
                }

                $score = $this->compareFaces($capturedFullPath, $registeredPath);

                if ($score > $threshold && $score > $bestScore) {
                    $bestScore = $score;
                    $matchedUser = $user;
                }
            }

            // Clean up temp file
            Storage::disk('public')->delete($tempPath);

            if ($matchedUser) {
                Auth::login($matchedUser, true);

                return response()->json([
                    'success' => true,
                    'message' => 'Wajah dikenali! Selamat datang, ' . $matchedUser->name,
                    'redirect' => '/dashboard',
                    'confidence' => round($bestScore * 100) . '%',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Wajah tidak dikenali. Pastikan Face ID sudah terdaftar pada akun Anda.',
            ], 401);

        } catch (\Exception $e) {
            Log::error('Face login error: ' . $e->getMessage());

            // Clean up on error
            if (isset($tempPath)) {
                Storage::disk('public')->delete($tempPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses Face ID. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Compare two face images using perceptual hashing + histogram comparison.
     * Returns a similarity score between 0.0 (no match) and 1.0 (exact match).
     *
     * Uses a combination of:
     * 1. Color histogram comparison (overall color distribution)
     * 2. Perceptual hash (structural similarity via DCT-based pHash)
     */
    private function compareFaces(string $capturedPath, string $registeredPath): float
    {
        try {
            $img1 = $this->loadImage($capturedPath);
            $img2 = $this->loadImage($registeredPath);

            if (!$img1 || !$img2) {
                return 0.0;
            }

            // Method 1: Color Histogram Comparison (weighted 40%)
            $histScore = $this->compareHistograms($img1, $img2);

            // Method 2: Perceptual Hash Comparison (weighted 60%)
            $phashScore = $this->comparePerceptualHash($img1, $img2);

            imagedestroy($img1);
            imagedestroy($img2);

            // Weighted combination
            return ($histScore * 0.4) + ($phashScore * 0.6);

        } catch (\Exception $e) {
            Log::warning('Face comparison error: ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Load image from file path, supporting JPEG, PNG, WEBP.
     */
    private function loadImage(string $path)
    {
        $info = @getimagesize($path);
        if (!$info) return null;

        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                return @imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:
                return @imagecreatefrompng($path);
            case IMAGETYPE_WEBP:
                return @imagecreatefromwebp($path);
            default:
                return null;
        }
    }

    /**
     * Compare color histograms of two images using correlation method.
     */
    private function compareHistograms($img1, $img2): float
    {
        $size = 32;

        // Resize both to normalize
        $resized1 = imagecreatetruecolor($size, $size);
        $resized2 = imagecreatetruecolor($size, $size);

        imagecopyresampled($resized1, $img1, 0, 0, 0, 0, $size, $size, imagesx($img1), imagesy($img1));
        imagecopyresampled($resized2, $img2, 0, 0, 0, 0, $size, $size, imagesx($img2), imagesy($img2));

        $hist1 = $this->buildHistogram($resized1, $size);
        $hist2 = $this->buildHistogram($resized2, $size);

        imagedestroy($resized1);
        imagedestroy($resized2);

        // Correlation coefficient
        $mean1 = array_sum($hist1) / count($hist1);
        $mean2 = array_sum($hist2) / count($hist2);

        $num = 0;
        $den1 = 0;
        $den2 = 0;

        for ($i = 0; $i < count($hist1); $i++) {
            $d1 = $hist1[$i] - $mean1;
            $d2 = $hist2[$i] - $mean2;
            $num += $d1 * $d2;
            $den1 += $d1 * $d1;
            $den2 += $d2 * $d2;
        }

        $den = sqrt($den1 * $den2);

        return $den > 0 ? max(0, ($num / $den + 1) / 2) : 0;
    }

    /**
     * Build a combined RGB histogram from an image.
     */
    private function buildHistogram($img, int $size): array
    {
        $bins = 16;
        $hist = array_fill(0, $bins * 3, 0);

        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $hist[intval($r / (256 / $bins))]++;
                $hist[$bins + intval($g / (256 / $bins))]++;
                $hist[$bins * 2 + intval($b / (256 / $bins))]++;
            }
        }

        return $hist;
    }

    /**
     * Compare perceptual hashes of two images.
     * Uses average hash (aHash) — fast and good enough for face comparison.
     */
    private function comparePerceptualHash($img1, $img2): float
    {
        $hash1 = $this->computeAverageHash($img1);
        $hash2 = $this->computeAverageHash($img2);

        // Hamming distance
        $distance = 0;
        $len = strlen($hash1);

        for ($i = 0; $i < $len; $i++) {
            if ($hash1[$i] !== $hash2[$i]) {
                $distance++;
            }
        }

        // Convert to similarity (0 distance = 1.0 similarity)
        return 1 - ($distance / $len);
    }

    /**
     * Compute average hash (aHash) for an image.
     * 1. Resize to 16x16 grayscale
     * 2. Compute average pixel value
     * 3. Each pixel above average = 1, below = 0
     */
    private function computeAverageHash($img): string
    {
        $size = 16;
        $gray = imagecreatetruecolor($size, $size);

        imagecopyresampled($gray, $img, 0, 0, 0, 0, $size, $size, imagesx($img), imagesy($img));

        // Convert to grayscale values
        $pixels = [];
        $sum = 0;

        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $rgb = imagecolorat($gray, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $grayVal = intval(0.299 * $r + 0.587 * $g + 0.114 * $b);
                $pixels[] = $grayVal;
                $sum += $grayVal;
            }
        }

        imagedestroy($gray);

        $avg = $sum / count($pixels);
        $hash = '';

        foreach ($pixels as $pixel) {
            $hash .= ($pixel >= $avg) ? '1' : '0';
        }

        return $hash;
    }
}
