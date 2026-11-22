import '../models/ukk/ukk_models.dart';
import 'api_client.dart';

class UkkService {
  UkkService({ApiClient? api}) : _api = api ?? ApiClient.instance;

  final ApiClient _api;

  Future<List<UkkExamItem>> getUjians() async {
    final response = await _api.get('/ukk/ujians');
    final list = response['data'] as List? ?? [];
    return list
        .map((item) => UkkExamItem.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  Future<({List<UkkRombelItem> rombels, List<UkkStudentItem> siswas, String peranPenguji})> getStudents(
    int ujianId, {
    int? rombelId,
  }) async {
    final query = <String, String>{};
    if (rombelId != null) {
      query['rombel_id'] = rombelId.toString();
    }

    final response = await _api.get('/ukk/ujians/$ujianId/siswas', query: query);
    final data = response['data'] as Map<String, dynamic>? ?? {};

    final rombelsRaw = data['rombels'] as List? ?? [];
    final siswasRaw = data['siswas'] as List? ?? [];
    final ujianRaw = data['ujian'] as Map<String, dynamic>? ?? {};

    return (
      rombels: rombelsRaw
          .map((item) => UkkRombelItem.fromJson(item as Map<String, dynamic>))
          .toList(),
      siswas: siswasRaw
          .map((item) => UkkStudentItem.fromJson(item as Map<String, dynamic>))
          .toList(),
      peranPenguji: ujianRaw['peran_penguji']?.toString() ?? 'semua',
    );
  }

  Future<UkkAssessmentDetailResponse> getStudentAssessment(
    int ujianId,
    int siswaId,
  ) async {
    final response = await _api.get('/ukk/ujians/$ujianId/siswas/$siswaId');
    final data = response['data'] as Map<String, dynamic>;
    return UkkAssessmentDetailResponse.fromJson(data);
  }

  Future<void> saveAssessment({
    required int ujianId,
    required int siswaId,
    required Map<String, int?> pengetahuan,
    required Map<String, String> catatanPengetahuan,
    required Map<String, int?> keterampilan,
    Map<String, dynamic>? sikap,
    Map<String, dynamic>? bilingual,
  }) async {
    final body = <String, dynamic>{
      'pengetahuan': pengetahuan,
      'catatan_pengetahuan': catatanPengetahuan,
      'keterampilan': keterampilan,
      if (sikap != null) 'sikap': sikap,
      if (bilingual != null) 'bilingual': bilingual,
    };

    await _api.post('/ukk/ujians/$ujianId/siswas/$siswaId', body: body);
  }
}
