package ch.isproject.recsysWb.contentRecommender;

import java.sql.Connection;
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
		
	public static final String SIMILARITY_TABLE_NAME = "{recsys_wb_content_similarity}";

	private DrupalConnection drupalConnection;
	private Map<Integer, Map<Integer, Double>> tfidfVectors;
    private SimilarityAlgorithm similarityAlgorithm;
    private Connection databaseBatchConnection;
	private int appId;

	
    public RunContentRecommender(CommandRecord record, Druplet druplet) {
    	super(record,druplet);

    	this.record = record;
    	this.druplet = druplet;
    	this.drupalConnection = druplet.getDrupalConnection();
    	this.tfidfVectors = new HashMap<Integer, Map<Integer,Double>>();
    	
    	// TODO create similarity according to appID from record
    	this.similarityAlgorithm = new CosineSimilarity();
    	this.appId = 1;
    	this.record.setId1((long) this.appId);
    }
    
    public Map<Integer, Map<Integer, Double>> getTFIDFValuesFromDatabase() {
    	logger.info("Getting documents from database");
    	Map<Integer, Map<Integer, Double>> tfidfValues = 
    			new HashMap<Integer, Map<Integer,Double>>();
    	try {
    		List<Map<String, Object>> result;
			result = this.drupalConnection.query("SELECT entity_id,tfidf_vector" 
					+ " FROM " + RunTFIDFCreator.TFIDF_TABLE_NAME);
			
			if ( result.size() == 0 )
				throw new RuntimeException("No TFIDF values in database. Generate them first!");
			
			for (Map<String, Object> map : result) {
				Map<Integer, Double> tmp = createMapFromString(
						(String) map.get("tfidf_vector"));
				tfidfValues.put( (Integer) map.get("entity_id") , tmp);
			}
			
		} catch (SQLException e) {
			String message = RecsysWbUtil.getPrintableStacktrace(e);
			logger.severe(message);
		}
    	
    	return tfidfValues;
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

    private void cleanupDatabase() {
    	try {
	    	this.databaseBatchConnection = this.drupalConnection
	    			.getConnection();
	    	
	    	boolean transactionIsSupported = this.databaseBatchConnection
	    			.getMetaData().supportsTransactionIsolationLevel(
	    					Connection.TRANSACTION_READ_COMMITTED);
	    	if ( transactionIsSupported ) {
	    		this.databaseBatchConnection.setTransactionIsolation(
	            		Connection.TRANSACTION_READ_COMMITTED);
	        }
	    	
	    	String deleteSqlCommand = this.drupalConnection.d("DELETE FROM " 
	    			+ SIMILARITY_TABLE_NAME + " WHERE app_id = ?");
	    	
	    	BatchUploader cleanupBatchJob = new BatchUploader(null, 
	    			"Delete-Batch", this.databaseBatchConnection, 
	    			deleteSqlCommand, this.drupalConnection.getMaxBatchSize());
	    	cleanupBatchJob.start();
	    	cleanupBatchJob.put(this.appId);
	    	cleanupBatchJob.accomplish();
	    	cleanupBatchJob.join();
    	} 
    	catch ( Exception e ) {
    		String message = RecsysWbUtil.getPrintableStacktrace(e);
			logger.severe(message);
		}
	}
    
	@Override
    protected void beforeExecute() {
    	super.beforeExecute();
    	this.tfidfVectors = this.getTFIDFValuesFromDatabase();
    	this.cleanupDatabase();
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
    	
    	String insertSql = this.drupalConnection.d("INSERT INTO " 
    			+ SIMILARITY_TABLE_NAME + " (app_id, source_entity_id, "
    			+ "target_entity_id, similarity) VALUES(?, ?, ?, ?)");
    	BatchUploader valueUploader = new BatchUploader(null, 
    			"Similarity-Batch", this.databaseBatchConnection, insertSql,
				this.drupalConnection.getMaxBatchSize());
		valueUploader.start();
		
		logger.info("Starting calculation ...");
    	
		// Just to display to progress properly
		double totalCalculations = documentIds.length * ( documentIds.length / 2 );
		List<Integer> printedProgress = new ArrayList<Integer>();
		
		long startTime = System.currentTimeMillis();
		
		for (int i = 0; i < documentIds.length; i++) {
			for (int j = i+1; j < documentIds.length; j++) {
				
				double similarity = this.calculateSimilarity( 
						this.tfidfVectors.get( documentIds[i] ), 
						this.tfidfVectors.get( documentIds[j] ) );
				
 				valueUploader.put(this.appId, documentIds[i], documentIds[j],
 						similarity);
 			
			}
			
			double progressInPercent = ((double)(i * documentIds.length / 2)) / totalCalculations * 100;
			if ( ((int) progressInPercent ) % 5 == 0 && ! printedProgress.contains((int)progressInPercent) ) {
				logger.info("Progress: " + (int) progressInPercent + "% ...");
				printedProgress.add((int) progressInPercent);
			}
    	}
		
    	try {
    		valueUploader.accomplish();
    		valueUploader.join();
    		
            this.databaseBatchConnection.commit();
            this.databaseBatchConnection.close();
    	} catch (Exception e) {
    		String message = RecsysWbUtil.getPrintableStacktrace(e);
    		logger.severe(message);
    	}
    	
    	long endTime = System.currentTimeMillis();
    	int seconds = (int) ((endTime - startTime) / 1000);
    	int minutes = seconds / 60;
    	int hours = minutes / 60;
    	
		this.record.setNumber2((float)documentIds.length);
    	this.record.setNumber3((float)totalCalculations);
    	this.record.setMessage("(Time spent: " + hours + "h" + minutes + "m" 
    			+ seconds + "s)");
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

