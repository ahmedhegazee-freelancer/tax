<?php

namespace App\Services;

final class SerializeService
{

    public static function hashedSerializedData($documentStructure)
    {
        $serializedData = self::serialize($documentStructure);
        return hash('sha256', $serializedData);
    }

    public static function serialize($documentStructure)
    {
        if (!is_array($documentStructure)) {
            if (gettype($documentStructure) == 'double' || gettype($documentStructure) == 'integer') {
                return '"' . number_format($documentStructure, 2, '.', '') . '"';
            }
            return '"' . $documentStructure . '"';
        }

        $serializedString = "";

        foreach ($documentStructure as $item => $value) {

            if (!is_array($value)) {
                $serializedString .= strtoupper('"' . $item . '"');
                $serializedString .= self::serialize($value);
            }

            if (is_array($value)) {
                $serializedString .= strtoupper('"' . $item . '"');
                foreach ($value as $subItem => $subValue) {
                    if (is_int($subItem))
                        // dump($subItem, $subValue);
                        $serializedString .= is_int($subItem) ? strtoupper('"' . $item . '"') : strtoupper('"' . $subItem . '"');
                    $serializedString .= self::serialize($subValue);
                }
            }
        }

        return $serializedString;
    }
}
