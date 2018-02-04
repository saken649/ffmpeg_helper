<?php
$ffmpeg = new FFMpeg();
$ffmpeg->main($argv);

class FFMpeg
{
    public function main(array $argv): void
    {
        try {
            if (!isset($argv[1])) throw new Exception('動画パスが渡されていません。');
            $list = $this->_getFileList($argv[1]);
            foreach ($list as $file) {
                $this->_convertFile($file);
            }
        } catch (Exception $e) {
            exit($e->getMessage() . "\n");
        }
    }

    private function _getFileList(string $path): array
    {
        $glob = glob($path . '*.mkv');
        if (empty($glob)) throw new Exception($path . 'にMKVファイルがありません。');
        return $glob;
    }

    private function _convertFile($file): void
    {
        $pathinfo = pathinfo($file);
        $dirname = $this->_escapeSpaces($pathinfo['dirname']);
        $convertedDir = $this->_mkdirConvertedDir($dirname);
        $to = sprintf('%s/%s.mp4', $convertedDir, $pathinfo['filename']);
        $file = $this->_escapeSpaces($file);
        $to = $this->_escapeSpaces($to);
        $cmd = sprintf('ffmpeg -i %s -crf 23 -tune animation %s', $file, $to);
        passthru($cmd);
        echo sprintf(">>>>> converted! <<<<<\nfrom: %s\nto: %s\n", $file, $to);
    }

    private function _mkdirConvertedDir($dirname): string
    {
        $convertedDir = sprintf('%s/converted', $dirname);
        if (!file_exists($convertedDir)) {
            if (!mkdir($convertedDir)) throw new Exception($convertedDir . 'の作成に失敗しました。');
        }
        return $convertedDir;
    }

    private function _escapeSpaces($path): string
    {
        return preg_replace("/\s/", "\\ ", $path);
    }
}
