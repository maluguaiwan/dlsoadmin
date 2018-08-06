<?php
namespace app\index\controller;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class Index
{
    public function index()
    {
        $html = file_get_contents(ROOT_PATH.'word/test.html');
        $word = new PhpWord();
        /*
        $styleTable = [
            'align' => 'center',
            'borderSize' => '1',
            'borderRightColor' => '#666',
            'valign' => 'center'
        ];
        
        $word->addTableStyle('table', $styleTable);
        */
        //$phpWord = \PhpOffice\PhpWord\IOFactory::load(ROOT_PATH.'word/hello.doc');
        $section = $word->addSection();
        //$section->setStyle(['table'=>$styleTable]);
        Html::addHtml($section,$html);
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($word, "Word2007");
        $xmlWriter->save(ROOT_PATH.'word/new1.docx');
        echo $html;
    }

}
