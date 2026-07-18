import 'dart:async';
import 'dart:convert';

import 'package:http/http.dart' as http;

import '../../core/config/api_config.dart';

class ApiException implements Exception {
  const ApiException(this.message, {this.statusCode, this.errors = const {}});

  final String message;
  final int? statusCode;
  final Map<String, dynamic> errors;

  @override
  String toString() => message;
}

class ApiClient {
  ApiClient._();

  static final ApiClient instance = ApiClient._();

  String? _token;

  void setToken(String? token) {
    _token = token;
  }

  Future<Map<String, dynamic>> get(String path, {Map<String, String>? query}) {
    final uri = _uri(path, query);
    return _send(() => http.get(uri, headers: _headers()));
  }

  Future<Map<String, dynamic>> post(
    String path, {
    Map<String, dynamic>? body,
    bool authenticated = true,
  }) {
    final uri = _uri(path);
    return _send(
      () => http.post(
        uri,
        headers: _headers(authenticated: authenticated),
        body: jsonEncode(body ?? const {}),
      ),
    );
  }

  Uri _uri(String path, [Map<String, String>? query]) {
    final base = ApiConfig.baseUrl.replaceAll(RegExp(r'/$'), '');
    final normalizedPath = path.startsWith('/') ? path : '/$path';
    return Uri.parse('$base$normalizedPath').replace(queryParameters: query);
  }

  Map<String, String> _headers({bool authenticated = true}) {
    return {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      if (authenticated && _token != null) 'Authorization': 'Bearer $_token',
    };
  }

  Future<Map<String, dynamic>> _send(
    Future<http.Response> Function() request,
  ) async {
    try {
      final response = await request().timeout(ApiConfig.timeout);
      final dynamic decoded = response.body.isEmpty
          ? <String, dynamic>{}
          : jsonDecode(response.body);
      final data = decoded is Map<String, dynamic>
          ? decoded
          : <String, dynamic>{};

      if (response.statusCode < 200 || response.statusCode >= 300) {
        final errors = data['errors'] is Map<String, dynamic>
            ? data['errors'] as Map<String, dynamic>
            : <String, dynamic>{};
        throw ApiException(
          _firstError(errors) ??
              data['message']?.toString() ??
              'Permintaan belum dapat diproses.',
          statusCode: response.statusCode,
          errors: errors,
        );
      }

      return data;
    } on TimeoutException {
      throw const ApiException(
        'Server membutuhkan waktu terlalu lama. Periksa koneksi lalu coba lagi.',
      );
    } on http.ClientException {
      throw const ApiException('Tidak dapat terhubung ke server SISFO.');
    } on FormatException {
      throw const ApiException('Respons server tidak dapat dibaca.');
    }
  }

  String? _firstError(Map<String, dynamic> errors) {
    for (final value in errors.values) {
      if (value is List && value.isNotEmpty) return value.first.toString();
      if (value != null) return value.toString();
    }
    return null;
  }
}
