REM: install.bat for start_udp

@echo off
set regDir=HKEY_LOCAL_MACHINE\SYSTEM\ControlSet001\Services\

reg delete %regDir%workman_timer\Parameters
instsrv.exe start_udp remove