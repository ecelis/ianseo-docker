<?php

/**
 * @class
 * Retrieves information such as the proper name from a font TTF or OTF font file.
 * @source http://stackoverflow.com/questions/5668901/php-how-to-read-title-of-font-from-ttf-file
 */
class FontInfo
{

    /**
     * Contains information in the TTF 'name' table.
     */
    private $_arrInfo = null;

    /**
     * Constructor.
     * @param strFontPath Path to the font file.
     */
    function __construct($strFontPath)
    {
        $this->_arrInfo = $this->getFontInfo($strFontPath);
    }

    /**
     * Gets the information about the font at the specified path.
     * @param strFontPath
     * @return
     */
    private function getFontInfo($strFontPath)
    {
        $arrFontTags=[];
        // Open the file and read its contents.
        $obintJFile = fopen($strFontPath, "r");
        $strText = fread($obintJFile, filesize($strFontPath));
        fclose($obintJFile);

        $intOffsetStorage=0;

        // Grab information.
        $intNumberOfTables = hexdec($this->dec2ord($strText[4]) . $this->dec2ord($strText[5]));
        for ($intI = 0; $intI < $intNumberOfTables; $intI++) {
            $strTag = $strText[12 + $intI * 16] . $strText[12 + $intI * 16 + 1] . $strText[12 + $intI * 16 + 2] . $strText[12 + $intI * 16 + 3];

            if ($strTag == "name") {
                $intOffset = hexdec(
                    $this->dec2ord($strText[12 + $intI * 16 + 8]) . $this->dec2ord($strText[12 + $intI * 16 + 8 + 1]) .
                    $this->dec2ord($strText[12 + $intI * 16 + 8 + 2]) . $this->dec2ord($strText[12 + $intI * 16 + 8 + 3])
                );
                $intOffsetStorage = hexdec($this->dec2ord($strText[$intOffset + 4]) . $this->dec2ord($strText[$intOffset + 5]));
                $intNumberOfNameRecords = hexdec($this->dec2ord($strText[$intOffset + 2]) . $this->dec2ord($strText[$intOffset + 3]));
            }
        }

        $intStorageDecimal = $intOffsetStorage + $intOffset;
        $strStorageHexadecimal = strtoupper(dechex($intStorageDecimal));

        for ($intJ = 0; $intJ < $intNumberOfNameRecords; $intJ++) {
            //$platform_id_dec = hexdec( $this->dec2ord( $this->text[$this->ntOffset+6+$intJ*12+0]).$this->dec2ord( $this->text[$this->ntOffset+6+$intJ*12+1]));
            $intNameId = hexdec($this->dec2ord($strText[$intOffset + 6 + $intJ * 12 + 6]) . $this->dec2ord($strText[$intOffset + 6 + $intJ * 12 + 7]));
            $intStringLength = hexdec($this->dec2ord($strText[$intOffset + 6 + $intJ * 12 + 8]) . $this->dec2ord($strText[$intOffset + 6 + $intJ * 12 + 9]));
            $intStringOffset = hexdec($this->dec2ord($strText[$intOffset + 6 + $intJ * 12 + 10]) . $this->dec2ord($strText[$intOffset + 6 + $intJ * 12 + 11]));

            if (!empty($intNameId) && empty($arrFontTags[$intNameId])) {
                $arrFontTags[$intNameId]='';
                for ($intL = 0; $intL < $intStringLength; $intL++) {
                    if (ord($strText[$intStorageDecimal + $intStringOffset + $intL]) == "0") {
                        continue;
                    } else {
                        $arrFontTags[$intNameId] .= $strText[$intStorageDecimal + $intStringOffset + $intL];
                    }
                }
            }
        }

        return $arrFontTags;
    }

    /**
     * Converts decimal to hex using the ascii value.
     * @param intDecimal
     * @return
     */
    protected function dec2ord($intDecimal)
    {
        return $this->dec2hex(ord($intDecimal));
    }

    /**
     * Performs hexadecimal to decimal conversion with proper padding.
     * @param intDecimal
     * @return
     */
    protected function dec2hex($intDecimal)
    {
        return str_repeat("0", 2 - strlen(($strHexadecimal = strtoupper(dechex($intDecimal))))) . $strHexadecimal;
    }

    /**
     * Gets the copyright.
     * @return
     */
    public function getCopyright()
    {
        return $this->_arrInfo[0];
    }

    /**
     * Gets the font family.
     * @return
     */
    public function getFontFamily()
    {
        return $this->_arrInfo[1];
    }

    /**
     * Gets the sub font family.
     * @return
     */
    public function getFontSubFamily()
    {
        return $this->_arrInfo[2];
    }

    /**
     * Gets the font id.
     * @return
     */
    public function getFontId()
    {
        return $this->_arrInfo[3];
    }

    /**
     * Gets the font name.
     * @return
     */
    public function getFontName()
    {
        return $this->_arrInfo[4];
    }

}