<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 09/10/2018
 * Time: 11:03
 */

namespace AppBundle\Service;

class dataConverter
{
    /**
     * From https://stackoverflow.com/questions/1930297/html-to-plain-text-for-email/23988241#23988241
     * converted from c# to PHP
     *
     * @param $source
     * @return mixed|null|string|string[]
     */
    public static function stripHTML($source)
    {
        // Remove HTML Development formatting
        // Replace line breaks with space
        // because browsers inserts space
        $result = str_replace("\r",  " ",$source );
        // Replace line breaks with space
        // because browsers inserts space
        $result = str_replace("\n",  " ",$result );
        // Remove step-formatting
        $result = str_replace("\t",  "",$result );
        // Remove repeating spaces because browsers ignore them
        $result = preg_replace("/( )+/im",  " ", $result);

        // Remove html-Tag (prepare first by clearing attributes)
        $result = preg_replace("/<( )*html([^>])*>\s*/im",  "<html>", $result);
        $result = preg_replace("/(<( )*(\/)( )*html( )*>)/im",  "</html>", $result);
        $result = preg_replace("/(<html>)|(<\/html>)/im",  "", $result);

        // Remove the header (prepare first by clearing attributes)
        $result = preg_replace("/<( )*head([^>])*>/im",  "<head>", $result);
        $result = preg_replace("/(<( )*(\/)( )*head( )*>)/im",  "</head>", $result);
        $result = preg_replace("/(<head>).*(<\/head>)/im",  "", $result);

        // remove all scripts (prepare first by clearing attributes)
        $result = preg_replace("/<( )*script([^>])*>/im",  "<script>", $result);

        $result = preg_replace("/(<( )*(\/)( )*script( )*>)/im",  "</script>", $result);

        //$result = System.Text.RegularExpressions.Regex.Replace($result,
        //         "(<script>)([^(<script>\.</script>)])*(</script>)",
        //         "",
        //         System.Text.RegularExpressions.RegexOptions.IgnoreCase);
        $result = preg_replace("/(<script>).*(<\/script>)/im",  "", $result);

        // remove all styles (prepare first by clearing attributes)
        $result = preg_replace("/<( )*style([^>])*>/im",  "<style>", $result);
        $result = preg_replace("/(<( )*(\/)( )*style( )*>)/im",  "</style>", $result);
        $result = preg_replace("/(<style>).*(<\/style>)/im",  "", $result);

        // insert tabs in spaces of <td> tags
        $result = preg_replace("/<( )*td([^>])*>/im",  "\t", $result);

        // insert line breaks in places of <BR> and <LI> tags
        $result = preg_replace("/<( )*br( )*\/?>/im",  "\r", $result);
        $result = preg_replace("/<( )*li( )*>/im",  "\r", $result);

        // insert line paragraphs (double line breaks) in place
        // if <P>, <DIV> and <TR> tags
        $result = preg_replace("/<( )*div([^>])*>/im",  "\r\r", $result);
        $result = preg_replace("/<( )*tr([^>])*>/im",  "\r\r", $result);
        $result = preg_replace("/<( )*p([^>])*>/im",  "\r\r", $result);

        // Remove remaining tags like <a>, links, images,
        // comments etc - anything that's enclosed inside < >
        $result = preg_replace("/<[^>]*>/im",  "", $result);

        // replace special characters:
        $result = preg_replace("/ /im",  " ", $result);
        $result = preg_replace("/&bull;/im",  " * ", $result);
        $result = preg_replace("/&lsaquo;/im",  "<", $result);
        $result = preg_replace("/&rsaquo;/im",  ">", $result);
        $result = preg_replace("/&trade;/im",  "(tm)", $result);
        $result = preg_replace("/&frasl;/im",  "/", $result);
        $result = preg_replace("/&lt;/im",  "<", $result);
        $result = preg_replace("/&gt;/im",  ">", $result);
        $result = preg_replace("/&copy;/im",  "(c)", $result);
        $result = preg_replace("/&reg;/im",  "(r)", $result);

        // Remove all others. More can be added, see
        // http://hotwired.lycos.com/webmonkey/reference/special_characters/
        $result = preg_replace("/&(.{2,6});/im",  "", $result);

        // for testing
        //System.Text.RegularExpressions.Regex.Replace($result,
        //       this.txtRegex.Text,"",
        //       System.Text.RegularExpressions.RegexOptions.IgnoreCase);

        // make line breaking consistent
        $result = str_replace("\n",  "\r",$result );

        // Remove extra line breaks and tabs:
        // replace over 2 breaks with 2 and over 4 tabs with 4.
        // Prepare first to remove any whitespaces in between
        // the escaped characters and remove redundant tabs in between line breaks
        $result = preg_replace("/(\r)( )+(\r)/im",  "\r\r", $result);
        $result = preg_replace("/(\t)( )+(\t)/im",  "\t\t", $result);
        $result = preg_replace("/(\t)( )+(\r)/im",  "\t\r", $result);
        $result = preg_replace("/(\r)( )+(\t)/im",  "\r\t", $result);

        // Remove redundant tabs
        $result = preg_replace("/(\r)(\t)+(\r)/im",  "\r\r", $result);

        // Remove multiple tabs following a line break with just one tab
        $result = preg_replace("/(\r)(\t)+/im",  "\r\t", $result);

        // Initial replacement target string for line breaks
        $breaks = "\r\r\r";
        // Initial replacement target string for tabs
        $tabs = "\t\t\t\t\t";
        for ($index = 0; $index < strlen($result); $index++)
        {
            $result = str_replace($breaks,  "\r\r",$result );
            $result = str_replace($tabs,  "\t\t\t\t",$result );
            $breaks = $breaks . "\r";
            $tabs = $tabs . "\t";
        }

        //remove spaces at the beginning of a line
        $result = preg_replace("/^ +/im",  "", $result);

        //line breaks at the beginning/end is probably unwanted. Could be left over by removing <html>/<head>/<body>
        $result = trim($result);

        // That's it.
        return $result;
    }
}