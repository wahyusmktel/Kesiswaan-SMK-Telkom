import 'lateness_record.dart';

class SecurityDashboard {
  const SecurityDashboard({
    required this.verifiedToday,
    required this.studentsOutside,
    required this.lateToday,
    required this.recentLateness,
    required this.generatedAt,
  });

  final int verifiedToday;
  final int studentsOutside;
  final int lateToday;
  final List<LatenessRecord> recentLateness;
  final DateTime generatedAt;

  factory SecurityDashboard.fromJson(Map<String, dynamic> json) {
    final stats = json['stats'] as Map<String, dynamic>? ?? {};
    final recent = json['recent_lateness'] as List<dynamic>? ?? [];

    return SecurityDashboard(
      verifiedToday: (stats['verified_today'] as num?)?.toInt() ?? 0,
      studentsOutside: (stats['students_outside'] as num?)?.toInt() ?? 0,
      lateToday: (stats['late_today'] as num?)?.toInt() ?? 0,
      recentLateness: recent
          .map((item) => LatenessRecord.fromJson(item as Map<String, dynamic>))
          .toList(),
      generatedAt:
          DateTime.tryParse(json['generated_at']?.toString() ?? '') ??
          DateTime.now(),
    );
  }
}
