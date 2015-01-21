<?php
/**
 * @file
 * web_textual_analysis.php
 *
 * Web Textual Analysis Lib is a basic library of textual analysis functions.
 */


/********************
 *                  *
 * TF IDF FUNCTIONS *
 *                  *
 *******************/

/**
 * Term Frequency Inverse Document Frequency
 *
 * @param        $term
 * @param        $document
 * @param        $corpus
 * @param string $tf_type
 * @param bool   $log_idf
 *
 * @return float
 */
function tf_idf($term, $document, $corpus, $tf_type='raw', $log_idf=true) {
  return tf($term, $document, $tf_type) * idf($term, $corpus, $log_idf);
}

/**
 * TF IDF N Keywords
 *
 * @param        $document
 * @param        $corpus
 * @param string $tf_type
 * @param bool   $log_idf
 *
 * @return array
 */
function tf_idf_get_keywords($document, $corpus, $n, $tf_type='raw', $log_idf=true) {
  $terms = array();
  $doc = array_filter(explode(' ', $document));
  foreach ($doc as $term) {
    $terms[$term] = tf_idf($term, $document, $corpus, $tf_type, $log_idf);
  }
  arsort($terms, SORT_NUMERIC);
  return $terms;
}

/**
 * Term Frequency (with options)
 *
 * @param        $term
 * @param        $document
 * @param string $tf_type
 *
 * @return float
 */
function tf($term, $document, $tf_type='raw') {
  switch ($tf_type) {
    case 'bool':
    case 'boolean':
      return contains_term($term,$document);
    case 'log':
    case 'logarithmic':
      return log(term_freq_raw($term,$document)+1);
    case 'aug':
    case 'augmented':
      return 0.5 + (0.5 * term_freq_raw($term,$document)) / max_term_freq($document);
    case 'raw':
      return term_freq_raw($term,$document);
  }

  return -1;
}

/**
 * Inverse Document Frequency
 *
 * @param      $term
 * @param      $corpus
 * @param bool $log
 *
 * @return int
 */
function idf($term, $corpus, $log=true) {
  $num_contain = 0;
  foreach ($corpus as $document) {
    $num_contain += (int)contains_term($term,$document);
  }
  $idf = count($corpus) / max(array(1,$num_contain));
  return $log ? log($idf) : $idf;
}

/**
 * @param $term
 * @param $document
 *
 * @return int
 */
function term_freq_raw($term,$document) {
  $val = array_count_values(explode(' ', $document));
  return $val[$term];
}

/**
 * @param $document
 *
 * @return int
 */
function max_term_freq($document) {
  return (int)max(array_count_values($document));
}

/**
 * @param $term
 * @param $document
 *
 * @return bool
 */
function contains_term($term,$document) {
  return strpos($document, $term) !== false;
}

/**
 * Test for TF IDF function.
 *
 * @return string
 */
function tf_idf_test() {
  $result = '';

  // Use Moby Dick as our test corpus.
  $chapters = get_moby_dick_chapters();
  $chapter_strings = array();
  foreach ($chapters as $chapter) {
    $chapter_strings[] = implode(' ',$chapter);
  }

  // Make an array of $chapter, $keywords pairs.
  $data = array();
  foreach ($chapter_strings as $chapter) {
    $data[] = array(
      'text' => $chapter,
      'keywords' => tf_idf_get_keywords($chapter,$chapter_strings,5),
    );
  }

  foreach ($data as $d) {
    $keyword = '';
    foreach ($d['keywords'] as $k => $score) {
      $keyword = $k;
      break;
    }
    $result .= '<h1>' . implode(' ', array_slice(explode(' ', $d['text']), 0, 10)) . '</h1>';
    $result .= '<p>' . $keyword . '</p>';
  }

  return (string)$result;
}

/**
 * Helper function to pull chapters of Moby-Dick: Or, The White Whale from
 * Project Gutenberg for testing purposes.
 */
function get_moby_dick_chapters() {
  $text = explode("\n",file_get_contents('http://www.gutenberg.org/cache/epub/2701/pg2701.txt'));
  $chapters = array();
  foreach ($text as $line) {
    // Are we beginning a new chapter?
    if (strpos($line,'CHAPTER')===0) {
      $chapters[] = array();
    } elseif (count($chapters)) {
      // Otherwise, add this line to the current chapter.
      $chapters[count($chapters)-1][] = trim($line);
    }
  }
  return $chapters;
}

/**********************
 *                    *
 * SIMILARITY METRICS *
 *                    *
 *********************/

function w_shingling($document, $n=4) {
  $terms = strtok($document, ' ');
  $len = strlen($document);
  $shingles = array();
  for ($i = 0; $i < $len - $n; $i++) {
    $shingles[$i] = array();
    for ($j = 0; $j < $n; $j++) {
      $shingles[$i][$j] = $terms[$i+$j];
    }
  }
  return $shingles;
}

/**
 * @param $docA
 * @param $docB
 */
function shingling_similarity($docA, $docB) {
  ;
}
