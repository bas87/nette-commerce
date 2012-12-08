<?php

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class MagicHelpers extends Nette\Object
{
    /** @var SystemContainer */
    private $context;

    /**
     * @param SystemContainer $context 
     */
    public function __construct(SystemContainer $context)
    {
        $this->context = $context;
    }

    /**
     * @param string $helper
     * @return MagicHelpers 
     */
    public function loader($helper)
    {
        if (\method_exists($this, $helper)) {
            return \callback($this, $helper);
        }
    }

    /**
     * @param string $value
     * @return string
     */
    public function currency($value)
    {
        return \str_replace(' ', "\xc2\xa0", \number_format($value, 2, '.', ' ')) . "\xc2\xa0Kč";
    }

    /**
     * @param string $path
     * @param int $w
     * @param int $h
     * @param int $q
     * @return string 
     */
    public function thumbnail($path, $w = NULL, $h = NULL, $q = 85)
    {
        $PUBLIC_TEMP_DIR =  \WWW_DIR . '/media/thumbnail';

        if ($w === NULL && $h === NULL) {
            $w = 100;
        }

        $relPath = $path;
        $path = \WWW_DIR . $path;

        if (!\file_exists($path)) {
            return $relPath;
        }

        $info = \pathinfo($path);
        $ext = $info['extension'];
        $hash = \md5($path) . '_' . $w . '_' . $h . '_' . $q;
        $newPath = $PUBLIC_TEMP_DIR . \DIRECTORY_SEPARATOR . $hash . '.' . $ext;

        if (\file_exists($newPath)) {
            return \str_replace(\WWW_DIR, '', $newPath);
        }

        $image = \Nette\Image::fromFile($path);

        if ($w === NULL) {
            $w = ($image->getWidth() / $image->getHeight()) * $h;
        }

        if ($h === NULL) {
            $h = ($image->getHeight() / $image->getWidth()) * $w;
        }

        // save
        $image->resize($w, $h, \Nette\Image::FILL)
                ->crop('50%', '50%', $w, $h)
                ->save($newPath, $q);

        return \str_replace(\WWW_DIR, '', $newPath);
    }

    /**
     * @param string $text
     * @return string 
     */
    public function texy($text)
    {
        $texy = new \Texy();
        $texy->encoding = 'utf-8';
        $texy->allowedTags = \Texy::NONE;
        $texy->allowedStyles = \Texy::NONE;
        $texy->setOutputMode(\Texy::HTML4_TRANSITIONAL);
        return $texy->process($text);
    }
}