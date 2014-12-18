package ch.isproject.recsysWb;

import java.sql.Connection;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.TreeMap;

import org.drupal.project.async_command.*;

public class RunContentRecommender extends AsyncCommand {
	
	private static final String SQL_QUESTION_NODE_PARAMETER = "question";
	
	private DrupalConnection drupalConnection;
	private List<Map<String,Object>> documents;
    
    public RunContentRecommender(CommandRecord record, Druplet druplet) {
    	super(record,druplet);

    	this.record = record;
    	this.druplet = druplet;
    	this.drupalConnection = druplet.getDrupalConnection();
    }
    
    public void processRequest() {
    	logger.info("Starting caluclation");
    	String sqlQueryString = "SELECT body_value,entity_id FROM ";
    	sqlQueryString += "field_data_body WHERE bundle=?";
    	try {
    		this.documents = this.drupalConnection.query(sqlQueryString,
    				SQL_QUESTION_NODE_PARAMETER);
		} catch (SQLException e) {
			logger.severe(e.getStackTrace().toString());
		}
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
    	TFIDFCreator creator = new TFIDFCreator( this.documents, "entity_id", 
    			"body_value", this.record.getString1() );
    	
		Map<Integer, Map<Integer, Double>> vectors = 
				new HashMap<Integer, Map<Integer,Double>>();

    	try {
    		
    		logger.info("Preparing documents");
    		
    		creator.prepareDocuments();
    		
    		logger.info("Documents prepared, going to create TFIDF vectors");
    		
    		vectors = creator.createTFIDFVector();
    		
    		logger.info("TFIDF vectors created, uploading them to database");
    		
		} catch (Exception e) {
			logger.severe(e.getStackTrace().toString());
		}

    	int appId = 1;
    	String tableName = "{recsys_wb_tfidf_values}";
    	String insertSql = this.drupalConnection.d("INSERT INTO " + tableName
    			+ "(app_id, entity_id, word_id, tfidf_value, timestamp) " 
    			+ "VALUES(?, ?, ?, ?, ?)");
    	BatchUploader valueUploader;
    	
    	try {
        	Connection connection = this.drupalConnection.getConnection();

        	boolean transactionIsSupported = connection.getMetaData()
        			.supportsTransactionIsolationLevel(
        					Connection.TRANSACTION_READ_COMMITTED);
        	if ( transactionIsSupported ) {
                connection.setTransactionIsolation(
                		Connection.TRANSACTION_READ_COMMITTED);
            }
        	
			valueUploader = new BatchUploader(null, "Insert-Batch", 
					connection, insertSql,
					this.drupalConnection.getMaxBatchSize());
			valueUploader.start();
			
	    	// Write the results to the database
	    	for (Integer documentId : vectors.keySet() ) {
				for (Integer wordId : vectors.get(documentId).keySet()) {
					valueUploader.put(appId, documentId, wordId, 
							vectors.get(documentId).get(wordId),
							"" + System.currentTimeMillis() );
				}
			}
	    	
	    	valueUploader.accomplish();
	    	valueUploader.join();
	    	
	        connection.commit();
	        connection.close();
	        
	    	logger.info("Finished DB upload");
	    	
		} catch (SQLException e) {
			logger.severe(e.getStackTrace().toString());
		} catch (InterruptedException e) {
			logger.severe(e.getStackTrace().toString());
		}
    	
    	
    	// Calculate the cosine similarity
		Integer[] keys = vectors.keySet().toArray(new Integer[vectors.keySet().size()]);
//    	for (int i = 0; i < keys.length; i++) {
//			for (int j = i+1; j < keys.length; j++) {
//				double similarityScore = calculateSimilarity(vectors.get(keys[i]),vectors.get(keys[j]));
//				double weightedScore = similarityScore / (vectors.get(keys[i]).size() + vectors.get(keys[j]).size());
//				if ( weightedScore > 0.11 ) {
//					logger.info("Documents: " + keys[i] + "<-->" + keys[j] + " have a weighted similarity of " + similarityScore);
//					logger.info("Documents: " + keys[i] + "<-->" + keys[j] + " have a weighted similarity of " + weightedScore);
//				}
//			}
//		}
    	
    	// Calculate cosine similarity in improved arrays
    	for (int i = 0; i < keys.length; i++) {
			for (int j = i+1; j < keys.length; j++) {
				Map<Integer, Double> map0 = new TreeMap<Integer, Double>(vectors.get(keys[i]));
				Map<Integer, Double> map1 = new TreeMap<Integer, Double>(vectors.get(keys[j]));
								
				for (Integer integer : map0.keySet() ) {
					if ( ! map1.containsKey(integer) )
						map1.put(integer, (double) 0);
				}
				
				for ( Integer integer : map1.keySet() ) {
					if ( ! map0.containsKey(integer) )
						map0.put(integer, (double) 0);
				}
				
				List<Double> list0 = new ArrayList<Double>(map0.values());
				List<Double> list1 = new ArrayList<Double>(map1.values());
				double similarity = CosineSimilarity.cosineSimilarity(list0, list1);
				if ( similarity > 0.2 ) { 
					logger.info("Documents: " + keys[i] + "<-->" + keys[j] + " have a similarity of " + similarity);
//					logger.info("Maps:\n" + map0 + "\n" + map1 );
				}
			}
		}
    }

	private double calculateSimilarity(Map<Integer, Double> map0,
			Map<Integer, Double> map1) {
		List<Double> list0 = new ArrayList<Double>();
		List<Double> list1 = new ArrayList<Double>();
		int commonWords = 0;
		
		for (Integer wordId : map0.keySet()) {
			if ( map1.containsKey(wordId) ) {
				list0.add(map0.get(wordId));
				list1.add(map1.get(wordId));
				commonWords++;
			}
		}
		
		double similarity = CosineSimilarity.cosineSimilarity(list0, list1);
		return similarity * commonWords;
	}
}

