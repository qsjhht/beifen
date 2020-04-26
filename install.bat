REM: install.bat for start_udp
@echo off

set cxDir=%~dp0
set wraper=%cxDir%srvany.exe
set appDir=%cxDir%
set regDir=HKEY_LOCAL_MACHINE\SYSTEM\ControlSet001\Services\
set phpDir=D:\phpStudy\PHPTutorial\php\php-5.6.27-nts\


set cxPHP=%phpDir%php.exe

REM ######## install start_udp########
set cxSrv=start_udp
set cxApp=%appDir%start_udp.php

set cxReg=%regDir%%cxSrv%\Parameters\
instsrv %cxSrv% %wraper%
reg add %cxReg% /v AppDirectory /t REG_SZ /d "%phpDir%" /f
reg add %cxReg% /v Application /t REG_SZ /d "%cxPHP%" /f 
reg add %cxReg% /v AppParameters /t REG_SZ /d "%cxApp%" /f