import 'package:flutter_secure_storage/flutter_secure_storage.dart';

import '../models/app_user.dart';
import 'api_client.dart';

class AuthService {
  AuthService._();

  static final AuthService instance = AuthService._();
  static const _tokenKey = 'sisfo_mobile_token';
  static const FlutterSecureStorage _storage = FlutterSecureStorage();

  final ApiClient _api = ApiClient.instance;

  AppUser? currentUser;

  Future<AppUser> login({
    required String email,
    required String password,
  }) async {
    final response = await _api.post(
      '/login',
      authenticated: false,
      body: {
        'email': email.trim(),
        'password': password,
        'device_name': 'sisfo_flutter_android',
      },
    );
    final data = response['data'] as Map<String, dynamic>;
    final token = data['token'].toString();
    final user = AppUser.fromJson(data['user'] as Map<String, dynamic>);

    if (!user.isSecurity) {
      _api.setToken(token);
      await _api.post('/logout');
      _api.setToken(null);
      throw const ApiException(
        'Versi awal aplikasi mobile baru tersedia untuk role Security.',
        statusCode: 403,
      );
    }

    await _storage.write(key: _tokenKey, value: token);
    _api.setToken(token);
    currentUser = user;
    return user;
  }

  Future<AppUser?> restoreSession() async {
    final token = await _storage.read(key: _tokenKey);
    if (token == null || token.isEmpty) return null;

    _api.setToken(token);
    try {
      final response = await _api.get('/me');
      final user = AppUser.fromJson(response['data'] as Map<String, dynamic>);
      if (!user.isSecurity) {
        await clearLocalSession();
        return null;
      }
      currentUser = user;
      return user;
    } on ApiException catch (error) {
      if (error.statusCode == 401 || error.statusCode == 403) {
        await clearLocalSession();
        return null;
      }
      rethrow;
    }
  }

  Future<void> logout() async {
    try {
      await _api.post('/logout');
    } catch (_) {
      // Sesi lokal tetap harus dibersihkan ketika server tidak terjangkau.
    } finally {
      await clearLocalSession();
    }
  }

  Future<void> clearLocalSession() async {
    await _storage.delete(key: _tokenKey);
    _api.setToken(null);
    currentUser = null;
  }
}
