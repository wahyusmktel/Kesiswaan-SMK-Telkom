class ApiConfig {
  const ApiConfig._();

  static const String baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'https://sisfo.smktelkom-lpg.id/api',
  );

  static const Duration timeout = Duration(seconds: 30);
}
