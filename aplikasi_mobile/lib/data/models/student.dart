class Student {
  const Student({
    required this.id,
    required this.nis,
    required this.name,
    required this.className,
    required this.alreadyLateToday,
    this.gender,
    this.major,
  });

  final int id;
  final String nis;
  final String name;
  final String className;
  final bool alreadyLateToday;
  final String? gender;
  final String? major;

  factory Student.fromJson(Map<String, dynamic> json) {
    return Student(
      id: (json['id'] as num).toInt(),
      nis: json['nis']?.toString() ?? '-',
      name: json['name']?.toString() ?? '-',
      className: json['class_name']?.toString() ?? '-',
      alreadyLateToday: json['already_late_today'] == true,
      gender: json['gender']?.toString(),
      major: json['major']?.toString(),
    );
  }
}
