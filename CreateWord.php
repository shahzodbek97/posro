<?php

namespace App\Service;

use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D;
use PhpOffice\PhpWord\TemplateProcessor;

class CreateWord
{
    public function create(array $codes, array $qtys, array $names, array $prices, int $size)
    {
        $count = count($codes);
        $count_list = 0;
        switch ($size) {
            case 1: {
                    $temp = '110x62';
                    $wc = 350;
                    $hc = 50;
                    break;
                }
            case 2: {
                    $temp = '130x55';
                    $wc = 413;
                    $hc = 44;
                    break;
                }
            case 3: {
                    $temp = '70x35';
                    $wc = 223;
                    $hc = 28;
                    break;
                }
        }

        $tmp = new TemplateProcessor(public_path('word-temp/' . $temp . '.docx'));
        foreach ($qtys as $qty) {
            $count_list = $count_list + intval($qty);
        }

        $tmp->cloneRow('id', $count_list);
        for ($d = 1; $d <= $count_list; $d++) {
            $tmp->setValue('id#' . $d, '');
        }
        $k = 1;
        for ($i = 0; $i < $count; $i++) {
            for ($j = 0; $j < intval($qtys[$i]); $j++) {

                $tmp->setValue('code#' . $k, $codes[$i]);
                $tmp->setValue('title#' . $k, strtoupper($names[$i]));
                $tmp->setValue('price#' . $k, $prices[$i]);
                Storage::disk('img')->put($names[$i] . '.png', base64_decode(DNS1D::getBarcodePNG($codes[$i], "C39")));
                $tmp->setImageValue('barcode#' . $k, array('path' => public_path('product/barcode/' . $names[$i] . '.png'), 'width' => $wc, 'height' => $hc, 'ratio' => false));
                $k++;
            }
        }

        $tmp->saveAs(public_path('word-temp/barcode.docx'));

        if (PHP_SAPI !== 'cli') {
            return response()->download(public_path('word-temp/barcode.docx'));
        } else {
            return $count_list;
        }
    }
}
