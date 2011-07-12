@ECHO OFF
set PHP_PEAR_SYSCONF_DIR=C:\Program Files (x86)\PHP\v5.3
set PHP_PEAR_INSTALL_DIR=C:\Program Files (x86)\PHP\v5.3\pear
set PHP_PEAR_DOC_DIR=C:\Program Files (x86)\PHP\v5.3\docs
set PHP_PEAR_BIN_DIR=C:\Program Files (x86)\PHP\v5.3
set PHP_PEAR_DATA_DIR=C:\Program Files (x86)\PHP\v5.3\data
set PHP_PEAR_TEST_DIR=C:\Program Files (x86)\PHP\v5.3\tests

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