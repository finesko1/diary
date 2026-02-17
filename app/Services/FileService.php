<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    public function determineType(?string $mimeType = null): string
    {
        if (empty($mimeType)) {
            return 'other';
        }

        // Изображения
        if (Str::startsWith($mimeType, 'image/')) {
            return 'image';
        }

        // Видео
        if (Str::startsWith($mimeType, 'video/')) {
            return 'video';
        }

        // Аудио
        if (Str::startsWith($mimeType, 'audio/')) {
            return 'audio';
        }

        // Документы и офисные файлы - возвращаем 'file'
        $documentMimes = [
            // PDF
            'application/pdf',

            // Microsoft Word
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-word.document.macroEnabled.12',

            // Microsoft Excel
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel.sheet.macroEnabled.12',

            // Microsoft PowerPoint
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-powerpoint.presentation.macroEnabled.12',

            // OpenDocument
            'application/vnd.oasis.opendocument.text',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.oasis.opendocument.presentation',

            // Текстовые файлы
            'text/plain',
            'text/html',
            'text/css',
            'text/javascript',
            'application/json',
            'application/xml',
            'text/xml',
            'text/csv',

            // Другие документы
            'application/rtf',
            'application/x-tex',
            'application/epub+zip',
        ];

        if (in_array($mimeType, $documentMimes)) {
            return 'file';
        }

        // Архивы
        $archiveMimes = [
            'application/zip',
            'application/x-zip-compressed',
            'application/x-rar-compressed',
            'application/x-tar',
            'application/gzip',
            'application/x-gzip',
            'application/x-bzip2',
            'application/x-7z-compressed',
            'application/x-apple-diskimage',
        ];

        if (in_array($mimeType, $archiveMimes)) {
            return 'archive';
        }

        // По умолчанию
        return 'other';
    }

}
