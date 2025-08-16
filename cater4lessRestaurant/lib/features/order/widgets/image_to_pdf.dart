import 'dart:typed_data';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:pdf/widgets.dart' as pw;
import 'package:permission_handler/permission_handler.dart';
import 'dart:io';
import 'package:stackfood_multivendor_restaurant/common/widgets/custom_toast.dart';
import 'package:stackfood_multivendor_restaurant/helper/pdf_download_helper.dart';

Future<void> capturedImageToPdf({Uint8List? capturedImage, required String businessName, required String orderId}) async {
  if (capturedImage == null) return;

  final pdf = pw.Document();

  final image = pw.MemoryImage(capturedImage);

  pdf.addPage(
    pw.Page(
      build: (pw.Context context) {
        return pw.Center(
          child: pw.Image(image),
        );
      },
    ),
  );

  final output = await PdfDownloadHelper.getProjectDirectory(businessName);
  final file = File("${output.path}/${'invoice'.tr}-$orderId.pdf");

  await Permission.storage.request();

  if (await Permission.storage.isGranted) {
    await file.writeAsBytes(await pdf.save());
    ScaffoldMessenger.of(Get.context!).showSnackBar(SnackBar(
      backgroundColor: Colors.transparent,
      elevation: 0,
      content: CustomToast(text: '${'pdf_saved_at'.tr} ${file.path.replaceAll('/storage/emulated/0/', '')}', isError: false),
      duration: const Duration(seconds: 2),
    ));
  } else {
    ScaffoldMessenger.of(Get.context!).showSnackBar(SnackBar(
      backgroundColor: Colors.transparent,
      elevation: 0,
      content: CustomToast(text: 'permission_denied_cannot_download_the_file'.tr, isError: true),
      duration: const Duration(seconds: 2),
    ));
  }
}
