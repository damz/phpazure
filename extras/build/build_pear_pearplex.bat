@ECHO OFF

call setenv.bat

phing -f build_pear_pearplex.xml
pause