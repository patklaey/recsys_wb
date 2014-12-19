package ch.isproject.recsysWb.contentRecommender;

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
    }
}

