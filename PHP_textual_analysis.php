<?php
/**
 * @file
 * PHP_textual_analysis.php
 *
 * PHP-textual-analysis is a basic library of textual analysis functions.
 */


/**
 * Term Frequency Inverse Document Frequency
 *
 * @param        $term
 * @param        $document
 * @param        $corpus
 * @param string $tf_type
 *
 * @return float
 */
function tf_idf($term,$document,$corpus, $tf_type='raw') {
  return tf($term,$document,$tf_type) * idf($term,$corpus);
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
 * @param $term
 * @param $corpus
 *
 * @return int
 */
function idf($term,$corpus) {
  $num_contain = 0;
  foreach ($corpus as $document) {
    $num_contain += (int)contains_term($term,$document);
  }
  return log(count($corpus) / max(array(1,$num_contain)));
}

/**
 * @param $term
 * @param $document
 *
 * @return int
 */
function term_freq_raw($term,$document) {
  return (int)array_count_values($document)[$term];
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
  return strpos($document,$term) !== false;
}