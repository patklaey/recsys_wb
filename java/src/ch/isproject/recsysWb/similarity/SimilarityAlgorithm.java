package ch.isproject.recsysWb.similarity;

import java.util.List;

public interface SimilarityAlgorithm {

	/**
	 * Method to calculate cosine similarity between two documents.
	 * @param docVector1 : document vector 1 (a)
	 * @param docVector2 : document vector 2 (b)
	 * @return
	 */
	public abstract double execute(List<Double> featureVector0,
			List<Double> featureVector1);

}