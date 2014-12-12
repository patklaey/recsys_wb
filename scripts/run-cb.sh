# If you install the recommender module under 'sites/all/modules/recommeder' and 'sites/default/settings.php' is accessible, then you can run this script directly.
# Otherwise, please see README and make necessary configuration to make this script running correctly.

# config parameters
export DRUPAL_HOME=/usr/share/drupal7

DRUPAL_MODULE_HOME=${DRUPAL_HOME}/sites/all/modules
ASYNC_COMMAND_HOME=${DRUPAL_MODULE_HOME}/async_command
RECSYS_WB_HOME=${DRUPAL_MODULE_HOME}/recsys_wb
MAHOUT_HOME=${RECSYS_WB_HOME}/mahout

# please make sure you have java and php installed.
command -v java >/dev/null || { echo "Cannot find java program. Please install Java first and make sure the executable is under PATH."; exit 1;}
command -v php >/dev/null || { echo "Cannot find php program. Please install PHP first and make sure the executable is under PATH."; exit 1;}

# set CLASSPATH
CLASSPATH=$ASYNC_COMMAND_HOME/async-command.jar:$ASYNC_COMMAND_HOME/lib/*:$MAHOUT_HOME/*:${RECSYS_WB_HOME}/java/lib/*


# using the default config.properties file in the working directory, or fall back to use settings.php directly.
#java -cp $CLASSPATH org.drupal.project.recommender.RecommenderApp


# specify the location and filename of config.properties file. Default is the working directory
CONFIG_FILE=${RECSYS_WB_HOME}/config/config.properties
java -cp $CLASSPATH ch.isproject.recsysWb.ContentRecommenderApp -c $CONFIG_FILE

#$RECSYS_WB_HOME/scripts/evaluate.sh
