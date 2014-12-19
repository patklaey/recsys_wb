package ch.isproject.recsysWb.contentRecommender;

import java.sql.SQLException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.TreeMap;

import org.drupal.project.async_command.*;

import ch.isproject.recsysWb.RecsysWbUtil;
import ch.isproject.recsysWb.similarity.CosineSimilarity;
import ch.isproject.recsysWb.similarity.SimilarityAlgorithm;
import ch.isproject.recsysWb.tfidf.RunTFIDFCreator;

public class RunContentRecommender extends AsyncCommand {
		
	private DrupalConnection drupalConnection;
	private Map<Integer, Map<Integer, Double>> tfidfVectors;
    private SimilarityAlgorithm similarityAlgorithm;
	
    public RunContentRecommender(CommandRecord record, Druplet druplet) {
    	super(record,druplet);

    	this.record = record;
    	this.druplet = druplet;
    	this.drupalConnection = druplet.getDrupalConnection();
    	this.tfidfVectors = new HashMap<Integer, Map<Integer,Double>>();
    	
    	// TODO create similarity according to appID from record
    	this.similarityAlgorithm = new CosineSimilarity();
    }
    
    public void processRequest() {
    	try {
    		List<Map<String, Object>> result;
			result = this.drupalConnection.query("SELECT entity_id,tfidf_vector" 
					+ " FROM " + RunTFIDFCreator.TFIDF_TABLE_NAME);
			
			if ( result.size() == 0 )
				throw new RuntimeException("No TFIDF values in database. Generate them first!");
			
			for (Map<String, Object> map : result) {
				Map<Integer, Double> tmp = createMapFromString(
						(String) map.get("tfidf_vector"));
				this.tfidfVectors.put( (Integer) map.get("entity_id") , tmp);
			}
			
		} catch (SQLException e) {
			String message = RecsysWbUtil.getPrintableStacktrace(e);
			logger.severe(message);
		}
    }
    
    private Map<Integer, Double> createMapFromString(String vector) {

    	Map<Integer, Double> map = new HashMap<Integer, Double>();
    	vector = vector.replaceAll("[{}]", "");

        String[] pairs = vector.split(", ");
        for (int i = 0; i < pairs.length; i++) {
			String[] points = pairs[i].split("=");
			map.put(Integer.valueOf(points[0]), 
					Double.valueOf(points[1]));
		}
        return map;
	}

	@Override
    protected void beforeExecute() {
    	super.beforeExecute();
    	this.processRequest();
    }
    
    @Override
    protected void afterExecute() {
	    super.afterExecute();
	    logger.info("After execute");
    }
    
    @Override
    protected void execute() {
    	super.execute();
    	Integer[] documentIds = this.tfidfVectors.keySet().toArray(
    			new Integer[this.tfidfVectors.keySet().size()]);
    	
    	for (int i = 0; i < documentIds.length; i++) {
    		 for (int j = i+1; j < documentIds.length; j++) {
    			 double similarity = this.calculateSimilarity( 
    					 this.tfidfVectors.get( documentIds[i] ), 
    					 this.tfidfVectors.get( documentIds[j] ) );
    			 logger.info("Documents: " + documentIds[i] + "<-->" 
    					 + documentIds[j] + " : " + similarity );
    		 }
    	}
    }
    
    private double calculateSimilarity(Map<Integer, Double> documentVector1,
    		Map<Integer, Double> documentVector2) {
    	Map<Integer, Double> map1 = 
    			new TreeMap<Integer, Double>(documentVector1);
    	Map<Integer, Double> map2 = 
    			new TreeMap<Integer, Double>(documentVector2);
    	
    	for (Integer integer : map1.keySet() ) {
    		if ( ! map2.containsKey(integer) )
    			map2.put(integer, (double) 0);
    	}
    	
    	for ( Integer integer : map2.keySet() ) {
    		if ( ! map1.containsKey(integer) )
    			map1.put(integer, (double) 0);
    	}
    	
    	List<Double> features1 = new ArrayList<Double>(map1.values());
    	List<Double> features2 = new ArrayList<Double>(map2.values());
    	return this.similarityAlgorithm.execute(features1, features2);
    }
}

