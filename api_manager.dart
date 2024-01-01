import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;

class ApiManager {
  final String baseUrl;

  ApiManager({required this.baseUrl});

  Future<dynamic> createHotel(File image, String name, String description, String price, String location) async {
    try {
      final uri = Uri.parse(baseUrl + 'createHotel.php');
      var request = http.MultipartRequest('POST', uri)
        ..files.add(await http.MultipartFile.fromPath('image', image.path))
        ..fields['name'] = name
        ..fields['description'] = description
        ..fields['price'] = price
        ..fields['location'] = location;

      var response = await request.send();
      var responseBody = await response.stream.bytesToString();

      if (response.statusCode == 200) {
        final jsonResponse = jsonDecode(responseBody);
        return jsonResponse['message'];
      } else {
        return 'Failed to upload image. Status Code: ${response.statusCode}';
      }
    } catch (e) {
      return 'Error uploading image: $e';
    }
  }

  Future<List<dynamic>> getHotels() async {
    try {
      var response = await http.get(
        Uri.parse('$baseUrl/get_Hotel.php'),
      );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return [];
      }
    } catch (e) {
      print('Error: $e');
      return [];
    }
  }

  Future<dynamic> updateHotel(File image, String name, String description, String price, String location) async {
    try {
      final uri = Uri.parse(baseUrl + 'updateHotel.php');
      var request = http.MultipartRequest('PUT', uri)
        ..files.add(await http.MultipartFile.fromPath('image', image.path))
        ..fields['name'] = name
        ..fields['description'] = description
        ..fields['price'] = price
        ..fields['location'] = location;

      var response = await request.send();
      var responseBody = await response.stream.bytesToString();

      if (response.statusCode == 200) {
        final jsonResponse = jsonDecode(responseBody);
        return jsonResponse['message'];
      } else {
        return 'Edit data hotel gagal. Status Code: ${response.statusCode}';
      }
    } catch (e) {
      return 'Error uploading image: $e';
    }
  }

  Future<dynamic> deleteHotel(String id) async {
    try {
      var response = await http.delete(
        Uri.parse('$baseUrl/deleteHotel.php'),
        body: {'id': id},
      );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return null;
      }
    } catch (e) {
      print('Error: $e');
      return null;
    }
  }
}