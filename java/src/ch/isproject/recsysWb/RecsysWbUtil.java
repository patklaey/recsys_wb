package ch.isproject.recsysWb;

public class RecsysWbUtil {
	
	public static String getPrintableStacktrace( Exception e ){
		String message = "";
		StackTraceElement[] elements = e.getStackTrace();
		
		do {
			message += e.getMessage() + "\n";
			if (e.getCause() != null )
				message += e.getCause() + "\n";
			
			e = (Exception) e.getCause();
		} while ( e != null );
		
		for (int i = 0; i < elements.length; i++) {
			message += "    at " + elements[i].toString() + "\n";
		}
		
		return message;
	}

}
