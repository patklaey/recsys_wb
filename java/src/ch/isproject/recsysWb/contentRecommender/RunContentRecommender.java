package ch.isproject.recsysWb.contentRecommender;

import java.sql.SQLException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.drupal.project.async_command.*;

import ch.isproject.recsysWb.RecsysWbUtil;
import ch.isproject.recsysWb.tfidf.RunTFIDFCreator;

public class RunContentRecommender extends AsyncCommand {
		
	private DrupalConnection drupalConnection;
	private Map<Integer, Map<Integer, Double>> tfidfVectors;
    
    public RunContentRecommender(CommandRecord record, Druplet druplet) {
    	super(record,druplet);

    	this.record = record;
    	this.druplet = druplet;
    	this.drupalConnection = druplet.getDrupalConnection();
    	this.tfidfVectors = new HashMap<Integer, Map<Integer,Double>>();
    }
    
    public void processRequest() {
    	try {
    		List<Map<String, Object>> result;
			result = this.drupalConnection.query("SELECT entity_id,tfidf_vector" 
					+ " FROM " + RunTFIDFCreator.TFIDF_TABLE_NAME);
						
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
    	
    	logger.info("The map currently looks like: " + this.tfidfVectors );

    }
}

