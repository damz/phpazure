@ECHO OFF
call setenv.bat

pear config-set temp_dir c:\temp

pear install --alldeps pear/PhpDocumentor

pear channel-discover pear.pearplex.net

pear channel-discover pear.phing.info
pear install --alldeps phing/phing

pear channel-discover pear.domain51.com
pear config-set preferred_state alpha
pear install --alldeps -f pear.domain51.com/Phing_d51PearPkg2Task
pear config-set preferred_state stable

pause