package ch.isproject.recsysWb;

import org.drupal.project.async_command.CommandLineLauncher;
import org.drupal.project.async_command.Druplet;
import org.drupal.project.async_command.DrupletConfig;
import org.drupal.project.async_command.Identifier;

@Identifier("tfidfCreator")
public class TFIDFCreatorApp extends Druplet {
	
    public TFIDFCreatorApp(DrupletConfig config) {
        super(config);
        registerCommandClass(RunTFIDFCreator.class);
    }
	
    public static void main(String[] args) {
        CommandLineLauncher launcher = new CommandLineLauncher(TFIDFCreatorApp.class);
        launcher.launch(args);
    }

}
