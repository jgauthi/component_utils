<?php
namespace Jgauthi\Component\Utils;

use InvalidArgumentException;
use SimpleXMLElement;

class Arrays
{
    /**
     * Combine multiple arrays to one.
     *
     * @param array $args
     * @return null|array
     */
    static public function combine(...$args): ?array
    {
        if (empty($args)) {
            return null;
        } elseif (1 === count($args) && !empty($args[0])) {
            $args = $args[0];
        }

        $args_keys = array_keys($args);
        sort($args_keys);
        $data = [];

        foreach ($args_keys as $product) {
            foreach ($args[$product] as $col_name => $col_value) {
                if (!isset($data[$col_name])) {
                    $data[$col_name] = array_fill_keys($args_keys, null);
                }

                $data[$col_name][$product] = $col_value;
            }
        }
        ksort($data);

        return $data;
    }

    /**
     * Improved implode: Use key + value.
     *
     * @param string $line separator between data
     * @param array $data
     * @param string $separator (optional, default =) separator between key and value
     * @param string|null $firstLine (optional, default null) string in first line
     *
     * @return string|null
     */
    static public function implode(string $line, array $data, string $separator = '=', ?string $firstLine = null): ?string
    {
        if (!is_array($data) || 0 === count($data)) {
            return null;
        }

        foreach ($data as $id => $value) {
            if (!isset($chaine)) {
                $chaine = $firstLine;
            } else {
                $chaine .= $line;
            }

            $chaine .= $id.$separator.$value;
        }

        return $chaine;
    }

    /**
     * Retourne un array sous forme de tableau html.
     *
     * @param array  $data        array( array('title1' => 'val1', 'title2' => 'val2'), array(...) )
     * @param string|null 		  $title_table optional
     * @param string $encode      UTF-8 or ISO-8859-1
     *
     * @return string HTML Table
     */
    static public function to_html_table(array $data, ?string $title_table = null, string $encode = 'UTF-8'): string
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Argument data is empty or is not an array.');
        }

        // Récupérer les données du 1er élément
        foreach ($data as $id => $array) {
            $first_id = $id;
            if (!is_array($array)) {
                throw new InvalidArgumentException('Array no compatible');
            }

            break;
        }

        $html = '<table class="table table-striped table-hover table-bordered">';
        if (!empty($title_table)) {
            $html .= '<caption>'.htmltxt($title_table, $encode).'</caption>';
        }

        // Titre
        $html .= '<thead class="thead-dark"><tr>';
        foreach (array_keys($data[$first_id]) as $key => $title) {
            $html .= "\n\t".'<th scope="col" class="th_'.$key.'">'.htmltxt($title, $encode).'</th>';
        }

        $html .= '</tr></thead>';

        // Contenu
        $html .= '<tbody>';
        foreach ($data as $trid => $array) {
            $html .= '<tr class="tr_'.$trid.'">';
            foreach ($array as $tdid => $content) {
                if (null === $content || '' === $content) {
                    $content = '&nbsp;';
                } else {
                    $content = nl2br(Html::convertUrlInString(htmltxt(trim($content), $encode)));
                }

                $html .= "\n\t".'<td class="td_'.$tdid.'">'.$content.'</td>';
            }

            $html .= '</tr>';
        }
        $html .= '</tbody>
	<tfoot>
		<tr>
			<td colspan="'.count($data[$first_id]).'">'.count($data).' elements in this table</td>
		</tr>
	</tfoot>
	</table>';

        return $html;
    }

    /**
     * Retourne un array sous forme de tableau html avec 3 colonnes (ligne => titre, colonne => produit).
     *
     * @param array $data [ 'title1' => ['product1' => 'val1', 'product2' => 'val2'), 'title2' => [...]] ]
     * @param string|null $title_table optional
     * @param string $encode UTF-8 or ISO-8859-1
     *
     * @return string HTML Table
     */
    static public function to_html_table_title_cmp(array $data, ?string $title_table = null, string $encode = 'UTF-8'): string
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Argument data is empty or is not an array.');
        }

        // Récupérer les données du 1er élément
        foreach ($data as $id => $array) {
            $first_id = $id;
            if (!is_array($array)) {
                throw new InvalidArgumentException('Array no compatible.');
            }

            break;
        }

        $html = '<table class="table table-striped table-hover table-bordered" border="1">';
        if (!empty($title_table)) {
            $html .= '<caption>'.htmltxt($title_table, $encode).'</caption>';
        }

        // Titre
        $title_list = array_keys($data[$first_id]);
        $html .= '<thead class="thead-dark"><tr><th scope="row">Colonnes</th>';
        foreach ($title_list as $title) {
            $html .= '<th scope="col">'.htmltxt($title, $encode).'</th>';
        }

        $html .= '</tr></thead>';

        // Contenu
        $html .= '<tbody>';
        foreach ($data as $col => $array) {
            $html .= '<tr><th align="left" scope="row">'.htmltxt($col, $encode).'</th>';
            foreach ($title_list as $title) {
                $content = ((isset($array[$title])) ? $array[$title] : null);

                if (null === $content || '' === $content) {
                    $content = '&nbsp;';
                } elseif (is_array($content)) {
                    $content = htmltxt(var_export($content, true), $encode);
                } else {
                    $content = nl2br(Html::convertUrlInString((htmltxt(trim($content), $encode))));
                }

                $html .= '<td>'.$content.'</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</tbody>
	<tfoot>
		<tr>
			<td colspan="'.(count($data[$first_id]) + 1).'">'.count($data).' elements in this table</td>
		</tr>
	</tfoot>
	</table>';

        return $html;
    }

    /**
     * Retourne un array sous forme de tableau html avec l'affichage du choix des colonnes et leur libellé).
     *
     * @param array $data [ 'title1' => ['product1' => 'val1', 'product2' => 'val2'], 'title2' => [...] ]
     * @param string|null $title_table optional
     * @param array $cols_display Liste des champs à afficher: [ 'code_champ' => 'libellé champ', 'code_champ2' => 'libellé champ2', ... ]
     * @param string $encode UTF-8 or ISO-8859-1
     *
     * @return string HTML Table
     */
    static public function to_html_table_title_filter_col(array $data, ?string $title_table, array $cols_display, string $encode = 'UTF-8'): string
    {
        if (empty($data) || !is_array($data)) {
            throw new InvalidArgumentException('Argument data is empty or is not an array.');
        }

        $html = '<table class="table table-striped" border="1">';
        if (!empty($title_table)) {
            $html .= '<caption>'.htmltxt($title_table, $encode).'</caption>';
        }

        // Titre
        $html .= '<thead class="thead-dark"><tr>';
        foreach ($cols_display as $key => $title) {
            $html .= '<th scope="col" class="'.$key.'">'.htmltxt($title, $encode).'</th>';
        }

        $html .= '</tr></thead>';

        // Contenu
        $html .= '<tbody>';
        foreach ($data as $col => $array) {
            $html .= '<tr>';
            foreach ($cols_display as $key => $title) {
                $content = null;
                if ('key' === $key) {
                    $content = $col;
                } elseif (isset($array[$key])) {
                    $content = $array[$key];
                }

                if (null === $content || '' === $content) {
                    $content = '&nbsp;';
                } elseif (is_array($content)) {
                    $content = htmltxt(var_export($content, true), $encode);
                } else {
                    $content = nl2br(Html::convertUrlInString((htmltxt(trim($content), $encode))));
                }

                $html .= '<td class="'.$key.'">'.$content.'</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</tbody>
	<tfoot>
		<tr>
			<td colspan="'.(count($cols_display) + 1).'">'.count($data).' elements in this table</td>
		</tr>
	</tfoot>
	</table>';

        return $html;
    }

    /**
     * Convert Array to XML.
     *
     * @param array  &$data
     * @param string $balise First html's balise
     * @param string|null $file Export to file, current output (php://output) or xml content
     *
     * @return string
     */
    static public function to_xml(array &$data, string $balise = '<?xml version="1.0"?><data></data>', ?string $file = null): string
    {
        $xml_data = new SimpleXMLElement($balise);

        // static public function call to convert array to xml
        self::to_xml_data($data, $xml_data);

        //saving generated xml file;
        if ('php://output' === $file) {
            if (!headers_sent()) {
                header('Content-Type: string/xml; charset=utf-8');
            }

            echo $xml_data->asXML();
        } elseif (!empty($file)) {
            return $xml_data->asXML($file);
        }

        return $xml_data->asXML();
    }

    /**
     * static public function definition to convert array to xml data.
     */
    static public function to_xml_data(array &$data, SimpleXMLElement &$xml_data): void
    {
        if (empty($data)) {
            return;
        }

        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item'.$key;
            } //dealing with <0/>..<n/> issues

            if (is_array($value)) {
                $subnode = $xml_data->addChild($key);
                self::to_xml_data($value, $subnode);
            } else {
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

    /**
     * Return an array with all values from a array with mutliples subvalues.
     *
     * @param array|object $array
     *
     * @return array
     */
    static public function values_recursive($array): array
    {
        $flat = [];

        if (empty($array)) {
            return $flat;
        } elseif (is_object($array)) {
            $array = (array) $array;
        }

        foreach ($array as $value) {
            if (is_array($value) || is_object($value)) {
                $flat = array_merge($flat, self::values_recursive($value));
            } else {
                $flat[] = $value;
            }
        }

        return $flat;
    }

    /**
     * Return an array with all values from a array with mutliples subvalues, using key as directory and added in value (recommande use with function "Folder::getArchitecture").
     *
     * @param array|object $array
     * @param array|null $regexp_filename
     * @param string|null $previus_dir
     *
     * @return array
     */
    static public function values_recursive_files($array, ?array $regexp_filename = null, ?string $previus_dir = null): array
    {
        $flat = [];

        if (is_object($array)) {
            $array = (array) $array;
        }

        foreach ($array as $key => $value) {
            if (is_array($value) || is_object($value)) {
                if (!empty($previus_dir)) {
                    $key = $previus_dir.DIRECTORY_SEPARATOR.$key;
                }

                $flat += self::values_recursive_files($value, $regexp_filename, $key);
                continue;
            }

            // Filter files
            $check_file = true;
            if (!empty($regexp_filename) && !preg_match_pattern($regexp_filename, $value)) {
                $check_file = false;
            }

            if ($check_file) {
                if (!empty($previus_dir)) {
                    $value = $previus_dir.DIRECTORY_SEPARATOR.$value;
                }

                $flat[] = $value;
            }
        }

        return $flat;
    }
}