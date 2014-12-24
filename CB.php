<?php

/**
 * Explain the basics of content based filtering
 */
function recsys_wb_explain_cb() {
  $title = "<strong><h3>Content Based Filtering</h3></strong>";
  $explanation = "In content based filtering the idea is to match (and recommend
) similar items based on their content. This content information can either be 
extracted automatically or added manually. If we know that the user Bob likes 
fantasy movies (from his user profile), and there is a new fantasy movie 
available we can recommend this movie to Bob. Or if Alice is reading an article 
about politics in the middle east, we can recommend further articles about the 
politics in the middle east to Alice. The good thing is, that there is no (at 
least not always) user profile necessary to recommend things. The bad thing 
however is, that most items must be enriched (often manually) with this content 
information (as for example \"this movie is a fantasy movie\").";
  return $title . $explanation;
}

/**
 * Explain the TFIDF function
 */
function recsys_wb_explain_tfidf() {
  $tfidf = 'TF\text{-}IDF(w,d) = TF(w,d) \times IDF(w)';
  $tf = 'TF(w,d) = {freq(w,d) \over max(freq(i,d), i \in otherWords(w,d))}';
  $idf = 'IDF(w) = \log{N \over n(w)}';
  
  $title = "<strong><h3>TF-IDF</h3></strong>";
  $explanation = "<div class='tex2jax'>";
  $explanation .= "TF-IDF stands for Term Frequency-Inverse Document Frequency 
and encodes a document as a vector in an n-dimensional Euclidean space. The 
space dimension is equal to the number of words in the document. As an example 
the TF-IDF value for a word " . mathInline("w") . " in document " 
. mathInline("d") . " is calculated as follows:";
  $explanation .= mathBlock($tf) . mathBlock($idf) . mathBlock($tfidf);
  $explanation .= "Where " . mathInline('freq(w,d)') . " denotes how many times 
the word " . mathInline("w") . " occurs in document " . mathInline("d") . ", "
. mathInline("otherWords(w,d)") . " specifies the set of all words of document "
. mathInline("d") . " except word " . mathInline("w") . ", " . mathInline("N")
. " is the total number of documents and " . mathInline("n(w)") . " is the 
number of documents in which the word " . mathInline("w") . " occurs at least 
once.<br/>";
  $explanation .= "Consider the following three sentences:";
  $explanation .= recsys_wb_document_example();
  $explanation .= "Let's have a look at an exmple: " 
. mathInline('TF\text{-}IDF(text,S_1)') . ":";
  $text_tf = 'TF(text,S_1) = {2 \over 2} = 1';
  $text_idf = 'IDF(text) = \log{3 \over 1} = 0.477';
  $text_tfidf = 'TF(text,S_1) \times IDF(text) = 1 \times 0.477 = 0.477';
  $explanation .= mathBlock($text_tf) . mathBlock($text_idf)
. mathBlock($text_tfidf) . mathBlock('TF\text{-}IDF(text,S_1) = 0.477');

  $explanation .= "Let's caluclate " 
. mathInline('TF\text{-}IDF(recommender,S_2)') . ":";
  $recommender_tf = 'TF(recommender,S_2) = {1 \over 1} = 1';
  $recommender_idf = 'IDF(recommender) = \log{3 \over 2} = 0.176';
  $recommender_tfidf = 'TF(recommender,S_2) \times IDF(recommender) = 1 \times
0.176 = 0.176';
  $explanation .= mathBlock($recommender_tf) . mathBlock($recommender_idf) 
. mathBlock($recommender_tfidf) 
. mathBlock('TF\text{-}IDF(recommender,S_2) = 0.176');
  $explanation .= "Last example " . mathInline('TF\text{-}IDF(are,S_3)') . ". As
 'are' is the same verb as 'is' just in another person, it is common to not 
calculate the TF-IDF for 'is' and 'are' but instead the TF-IDF of 'be' so;";
  $be_tf = 'TF(be,S_3) = {1 \over 1} = 1';
  $be_idf = 'IDF(be) = \log{3 \over 3} = 0';
  $be_tfidf = 'TF(be,S_3) \times IDF(be) = 1 \times 0 = 0';
  $explanation .= mathBlock($be_tf) . mathBlock($be_idf) . mathBlock($be_tfidf);
  $explanation .= mathBlock('TF\text{-}IDF(be,S_3) = 0');
  $explanation .= "As we can see, the TF-IDF value for the word 'be' in
sentence 3 is 0. In fact, every word which occurs in every document has an 
TF-IDF value of 0 (as " . mathInline('IDF(word) = \log{N \over N } = 0') . ").";
  return $title . $explanation . "</div>";
}

/**
 * Explain how to measure the similarity between documents
 */
function recsys_wb_explain_content_similarity() {
  $title = "<strong><h3>Content Similarity</h3></strong>";
  $explanation = "<div class='tex2jax'>";
  $explanation .= "The similarity between two (text) documents can be measured 
by comparing their TF-IDF vector representation. Consider again the following 
sentences: ";
  $explanation .= recsys_wb_document_example();
  $explanation .= "To comapre this two sentences, first all TF-IDF values are 
calculated: ";
  $explanation .= recsys_wb_content_similarity_example_table();
  return $title . $explanation . "</div>";
}


/**
 * Function to get some example text for TF-IDF explanation
 */
function recsys_wb_document_example() {
  $example = "<code>This is some text about a text recommender system</code>";
  $example .= "<code>A content based recommender system is cool</code>";
  $example .= "<code>Penguins are birds but can't fly</code>";
  return $example;
}

/**
 * Function to display the tfidf values of the example text sentences
 */
function recsys_wb_content_similarity_example_table() {
  $style = 'text-align:center;vertical-align:middle';
  $header = array(
    array( 'data' => ('Word'), 'style' => $style ),
    array( 'data' => t('Sentence 1'),'style' => $style ),
    array( 'data' => t('Sentence 2'),'style' => $style ),
    array( 'data' => t('Sentence 3'),'style' => $style ),    
  );
  $rows = array(
    array( 
      array( 'data' => 'this', 'style' => $style ),
      array( 'data' => 0.151, 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
    ),
    array(
      array( 'data' => 'is', 'style' => $style ),
      array( 'data' => 0, 'style' => $style ),
      array( 'data' => 0, 'style' => $style ),
    ),
    array(
      array( 'data' => 'some', 'style' => $style ),
      array( 'data' => 0.151, 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
    ),
    array(
      array( 'data' => 'text', 'style' => $style ),
      array( 'data' => 0.301, 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
    ),
    array(
      array( 'data' => 'about', 'style' => $style ),
      array( 'data' => 0.151, 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
    ),
    array(
      array( 'data' => 'a', 'style' => $style ),
      array( 'data' => 0, 'style' => $style ),
      array( 'data' => 0, 'style' => $style ),
    ),
    array(
      array( 'data' => 'recommender', 'style' => $style ),
      array( 'data' => 0, 'style' => $style ),
      array( 'data' => 0, 'style' => $style ),
    ),
    array(
      array( 'data' => 'system', 'style' => $style ),
      array( 'data' => 0, 'style' => $style ),
      array( 'data' => 0, 'style' => $style ),
    ),
    array(
      array( 'data' => 'content', 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
      array( 'data' => 0.301, 'style' => $style ),
    ),
    array(
      array( 'data' => 'based', 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
      array( 'data' => 0.301, 'style' => $style ),
    ),
    array(
      array( 'data' => 'cool', 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
      array( 'data' => 0.301, 'style' => $style ),
    ),
  );

  return theme( 
    'table',
    array( 
      'header' => $header, 
      'rows' => $rows, 
      'caption' => "TF-IDF values for both sentences" 
    )
  );
}

?>