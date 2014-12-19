package ch.isproject.recsysWb.similarity;

import java.util.List;

public class CosineSimilarity {
	
	/**
     * Method to calculate cosine similarity between two documents.
     * @param docVector1 : document vector 1 (a)
     * @param docVector2 : document vector 2 (b)
     * @return
     */
    public static double cosineSimilarity(List<Double> featureVector0, List<Double> featureVector1) {
        double dotProduct = 0.0;
        double magnitude0 = 0.0;
        double magnitude1 = 0.0;
        double cosineSimilarity = 0.0;
 
        for (int i = 0; i < featureVector0.size(); i++) {
            dotProduct += featureVector0.get(i) * featureVector1.get(i);
            magnitude0 += Math.pow(featureVector0.get(i), 2);
            magnitude1 += Math.pow(featureVector1.get(i), 2);
        }
 
        magnitude0 = Math.sqrt(magnitude0);
        magnitude1 = Math.sqrt(magnitude1);
 
        if (magnitude0 != 0.0 && magnitude1 != 0.0) {
            cosineSimilarity = dotProduct / (magnitude0 * magnitude1);
        }
        else {
            return 0.0;
        }
        
        return cosineSimilarity;
    }
}
