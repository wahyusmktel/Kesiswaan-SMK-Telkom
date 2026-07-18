class AppUser {
  const AppUser({
    required this.id,
    required this.name,
    required this.email,
    required this.roles,
    this.avatar,
  });

  final int id;
  final String name;
  final String email;
  final List<String> roles;
  final String? avatar;

  bool get isSecurity => roles.any((role) => role.toLowerCase() == 'security');

  String get primaryRole => roles.isEmpty ? '-' : roles.first;

  factory AppUser.fromJson(Map<String, dynamic> json) {
    final rolesJson = json['roles'];
    final roles = rolesJson is List
        ? rolesJson.map((role) => role.toString()).toList()
        : [if (json['role'] != null) json['role'].toString()];

    return AppUser(
      id: (json['id'] as num).toInt(),
      name: json['name']?.toString() ?? '-',
      email: json['email']?.toString() ?? '-',
      roles: roles,
      avatar: json['avatar']?.toString(),
    );
  }
}
