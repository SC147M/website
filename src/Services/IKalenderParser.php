<?php
/**
 * Created by PhpStorm.
 * User: christianlorenz
 * Date: 2019-07-07
 * Time: 13:13
 */

namespace App\Services;


use Symfony\Component\HttpClient\HttpClient;

class IKalenderParser
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    public function getDates($url): array
    {
        $response = $this->httpClient->request('GET', $url);
        $content = $response->getContent();

        $tables = preg_match(
            '/<td class="kalender_woche_tag kalender_woche_taglast">(.*)<\/td>/ims',
            $content,
            $matches
        );
        $table = trim(utf8_encode($matches[1]));
        $table = preg_replace('/\r?\n|\r/ims', '', $table);
        $table = preg_replace('/\s\s+/ims', ' ', $table);
        $parse1 = preg_replace('/\s*(?:style|valign|align|dir|height|width|class)\s*=\s*"[^"]*"\s*/ims', '', $table);

        preg_match('/<tablecellspacing="0" cellpadding="0">(.*)<\/table>/', $parse1, $m2);
        $m2[1] = str_replace('</tr>', '', $m2[1]);
        $rows = explode('<tr>', $m2[1]);


        $entries = [];

        foreach ($rows as $row) {
            $row = trim($row);
            if (substr_count($row, '<td colspan="6">') === 0 && !empty($row)) {
                $entries[] = $row;
            }
        }

        foreach ($entries as &$entry) {
            $cols = explode('<td>', $entry);
            $entry = [
                'date'  => strip_tags($cols[2]),
                'time'  => strip_tags($cols[3]),
                'entry' => strip_tags($cols[5]),
            ];


        }

        return $entries;

    }
}