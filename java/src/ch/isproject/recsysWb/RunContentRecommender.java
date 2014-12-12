package ch.isproject.recsysWb;

import java.sql.SQLException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.drupal.project.async_command.*;

public class RunContentRecommender extends AsyncCommand {
	
	private static final String SQL_QUESTION_NODE_PARAMETER = "question";
	
	private DrupalConnection databaseConnection;
	private List<Map<String,Object>> documents;
	
    public static RunContentRecommender create(long appId, Druplet druplet) {
        return create(appId, null, druplet);
    }

    public static RunContentRecommender create(long appId, String prefFilename, Druplet druplet) {
        Map<String, Object> fields = new HashMap<String, Object>();
        fields.put("id1", appId);
        CommandRecord record = CommandRecord.forge(fields);
        return new RunContentRecommender(record, druplet);
    }
    
    public RunContentRecommender(CommandRecord record, Druplet druplet) {
    	super(record,druplet);
    	
    	this.record = record;
    	this.druplet = druplet;
    	this.databaseConnection = druplet.getDrupalConnection();
    }
    
    public void processRequest() {
    	logger.info("Starting caluclation");
    	String sqlQueryString = "SELECT body_value,entity_id FROM ";
    	sqlQueryString += "field_data_body WHERE bundle=?";
    	try {
    		this.documents = this.databaseConnection.query(sqlQueryString,SQL_QUESTION_NODE_PARAMETER);
		} catch (SQLException e) {
			e.printStackTrace();
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
    	TFIDFCreator creator = new TFIDFCreator( this.documents,"entity_id","body_value");
    	try {
			creator.createTFIDFVector();
		} catch (Exception e) {
			logger.warning(e.getStackTrace().toString());
			e.printStackTrace();
		}
    	logger.info("Execute execute");
    }
    
}

