<?php
/**
 * @file
 * PHP_textual_analysis.php
 *
 * PHP-textual-analysis is a basic library of textual analysis functions.
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

  $corpus = array(
    'this is a sentence',
    'this is another sentence',
    'and this is yet one more',
    'and this is one where the words this and armadillo are repeated like this armadillo'
  );

  $result .= '<h1>TF IDF Test</h1>';
  $result .= '<h2>Corpus</h2>' . '<ul>';
  foreach ($corpus as $document) {
    $result .= '<li>' . $document . '</li>';
  }
  $result .= '</ul>';

  $result .= '<h2>Tests</h2>';
  foreach ($corpus as $document) {
    $result .= '<h3>' . $document . '</h3>' . '<ul>';
    foreach (explode(' ', $document) as $term) {
      $result .= '<li>' . $term . "<br><strong>" . tf_idf($term,$document,$corpus) . "</strong>"; 
      // $result .= "     tf: " . tf($term,$document); 
      // $result .= "     idf: " . idf($term,$corpus);
      $result .= '</li>';
    }
    $result .= '</ul>';
  }

  return (string)$result;
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

function shingling_similarity($docA, $docB) {
  ;
}