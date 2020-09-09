@echo OFF
set http_proxy=http://proxolo.cs.poste.it:8080
del /Q *.txt input_files\
start /WAIT cmd /c php splitter.php data/%1 %2
cd input_files\
ping 127.0.0.1 -n 4 >nul
FOR /F %%C in ('dir /a-d-s-h /b ^| find /C ".txt"') do set count=%%C
cd ..
echo %count%
if(%count% GEQ %3) do (
FOR /L %%A in (1,1,%count%) do (
start cmd /k php ip_finder.php %%A )
)