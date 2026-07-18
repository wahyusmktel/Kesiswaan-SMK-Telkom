import '../models/lateness_record.dart';
import '../models/security_dashboard.dart';
import '../models/student.dart';
import 'api_client.dart';

class SecurityService {
  SecurityService({ApiClient? api}) : _api = api ?? ApiClient.instance;

  final ApiClient _api;

  Future<SecurityDashboard> getDashboard() async {
    final response = await _api.get('/security/dashboard');
    return SecurityDashboard.fromJson(response['data'] as Map<String, dynamic>);
  }

  Future<Student> scanStudent(String code) async {
    final response = await _api.post(
      '/security/students/scan',
      body: {'code': code},
    );
    return Student.fromJson(response['data'] as Map<String, dynamic>);
  }

  Future<List<Student>> searchStudents(String query) async {
    final response = await _api.get(
      '/security/students/search',
      query: {'q': query},
    );
    final data = response['data'] as List<dynamic>? ?? [];
    return data
        .map((item) => Student.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  Future<LatenessRecord> recordLateness({
    required int studentId,
    required String reason,
  }) async {
    final response = await _api.post(
      '/security/lateness',
      body: {'master_siswa_id': studentId, 'reason': reason},
    );
    return LatenessRecord.fromJson(response['data'] as Map<String, dynamic>);
  }

  Future<List<LatenessRecord>> getTodayHistory() async {
    final response = await _api.get('/security/lateness/today');
    final data = response['data'] as List<dynamic>? ?? [];
    return data
        .map((item) => LatenessRecord.fromJson(item as Map<String, dynamic>))
        .toList();
  }
}
