<?php

class SimplePDF
{
    private $pages = [];          
    private $currentContent = '';
    private $pageWidth = 842;     
    private $pageHeight = 595;
    private $y = 0;
    private $marginTop = 40;
    private $marginLeft = 30;
    private $title = '';
    private $headerCallback;
    private $footerCallback;
    private $pageCount = 0;

    public function __construct($title = 'Laporan')
    {
        $this->title = $title;
    }

    public function setHeaderCallback(callable $cb)
    {
        $this->headerCallback = $cb;
    }

    public function setFooterCallback(callable $cb)
    {
        $this->footerCallback = $cb;
    }

    public function addPage()
    {
        if ($this->currentContent !== '') {
            $this->finishPage();
        }
        $this->pageCount++;
        $this->currentContent = '';
        $this->y = $this->pageHeight - $this->marginTop;

        if ($this->headerCallback) {
            call_user_func($this->headerCallback, $this);
        }
    }

    private function finishPage()
    {
        if ($this->footerCallback) {
            call_user_func($this->footerCallback, $this, $this->pageCount);
        }
        $this->pages[] = $this->currentContent;
    }

    private function esc($text)
    {
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
            return $converted !== false ? $converted : $text;
        }
        return $text;
    }

    public function checkPageBreak($neededHeight, callable $rebuildHeaderRow = null)
    {
        if ($this->y - $neededHeight < 40) {
            $this->addPage();
            if ($rebuildHeaderRow) {
                $rebuildHeaderRow($this);
            }
        }
    }

    public function text($x, $size, $text, $bold = false)
    {
        $font = $bold ? 'F2' : 'F1';
        $t = $this->esc($text);
        $this->currentContent .= "BT /$font $size Tf $x {$this->y} Td ($t) Tj ET\n";
    }

    public function line($x1, $x2)
    {
        $this->currentContent .= "{$x1} {$this->y} m {$x2} {$this->y} l S\n";
    }

    public function rect($x, $y, $w, $h)
    {
        $this->currentContent .= "{$x} {$y} {$w} {$h} re S\n";
    }

    public function moveDown($amount)
    {
        $this->y -= $amount;
    }

    public function getY() { return $this->y; }
    public function setY($y) { $this->y = $y; }
    public function getPageWidth() { return $this->pageWidth; }
    public function getPageHeight() { return $this->pageHeight; }

    public function tableRow($columns, $widths, $startX, $rowHeight = 16, $bold = false)
    {
        $x = $startX;
        $topY = $this->y + 11;
        foreach ($columns as $i => $col) {
            $this->rect($x, $topY - $rowHeight, $widths[$i], $rowHeight);
            $truncated = strlen($col) > 45 ? substr($col, 0, 42) . '...' : $col;
            $this->text($x + 3, 8, $truncated, $bold);
            $x += $widths[$i];
        }
        $this->moveDown($rowHeight);
    }

    public function output($filename = 'document.pdf')
    {
        if ($this->currentContent !== '') {
            $this->finishPage();
        }

        $objects = [];
        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";

        $kids = [];
        $pageObjStart = 4; 
        $fontRegularObj = 3;
        $fontBoldObj = 4;

        $objects[$fontRegularObj] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>";
        $objects[$fontBoldObj] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>";

        $objNum = 5;
        $pageObjNumbers = [];

        foreach ($this->pages as $content) {
            $contentObjNum = $objNum++;
            $pageObjNum = $objNum++;
            $pageObjNumbers[] = $pageObjNum;

            $stream = $content;
            $objects[$contentObjNum] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream";

            $objects[$pageObjNum] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$this->pageWidth} {$this->pageHeight}] "
                . "/Resources << /Font << /F1 {$fontRegularObj} 0 R /F2 {$fontBoldObj} 0 R >> >> "
                . "/Contents {$contentObjNum} 0 R >>";
        }

        $kidsRefs = implode(' ', array_map(fn($n) => "$n 0 R", $pageObjNumbers));
        $objects[2] = "<< /Type /Pages /Kids [{$kidsRefs}] /Count " . count($pageObjNumbers) . " >>";

        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $num => $body) {
            $offsets[$num] = strlen($pdf);
            $pdf .= "{$num} 0 obj\n{$body}\nendobj\n";
        }

        $xrefStart = strlen($pdf);
        $maxObj = max(array_keys($objects));
        $pdf .= "xref\n0 " . ($maxObj + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $maxObj; $i++) {
            if (isset($offsets[$i])) {
                $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
            } else {
                $pdf .= "0000000000 65535 f \n";
            }
        }
        $pdf .= "trailer\n<< /Size " . ($maxObj + 1) . " /Root 1 0 R >>\nstartxref\n{$xrefStart}\n%%EOF";

        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header("Content-Length: " . strlen($pdf));
        echo $pdf;
    }
}
