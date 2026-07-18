import 'student.dart';

class LatenessRecord {
  const LatenessRecord({
    required this.id,
    required this.uuid,
    required this.student,
    required this.reason,
    required this.status,
    required this.recordedAt,
  });

  final int id;
  final String uuid;
  final Student student;
  final String reason;
  final String status;
  final DateTime recordedAt;

  factory LatenessRecord.fromJson(Map<String, dynamic> json) {
    return LatenessRecord(
      id: (json['id'] as num).toInt(),
      uuid: json['uuid']?.toString() ?? '',
      student: Student.fromJson(json['student'] as Map<String, dynamic>),
      reason: json['reason']?.toString() ?? '-',
      status: json['status']?.toString() ?? '-',
      recordedAt:
          DateTime.tryParse(json['recorded_at']?.toString() ?? '') ??
          DateTime.now(),
    );
  }
}
