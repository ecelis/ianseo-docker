# Ianseo Scorekeeper NG

## Debug

If you are instructed to, please do the following to submit debug information to the developers:

- create a folder called "log" (all lowercase) inside the the ISK-NG folder and make it world-writeable
- open the competition as usual, then add `?ianseo-debug-session` (including the "**?**") at the URL
  
  - Ianseo will turn "brownish" to show it is in debug mode
  - only the browser where you modified the URL is in debug mode, letting other connected devices to behave normally
  - to go back to the usual bluish ianseo, just close the competition or quit the browser (or use a different browser) 

This procedure will start log the comunication between ianseo and the devices into a file called `messages-YYYY-MM-DD.log` where Y, M and D are current year, month and day

You will then need to send the file(s) of the problematic day(s) with the export of the competition to isk@ianseo.net