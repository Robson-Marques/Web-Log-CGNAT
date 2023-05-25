
O Script a abaixo foi utilizado no Debian 11 Server instalação limpa.

INSTALAÇÃO GOOGLE-DRIVE-OCAMLFUSE
========================================================================
instalando e iniciando alguns pacotes:

	$ update
	$ sudo apt update
	$ sudo apt install opam
	$ sudo apt install m4 libcurl4-gnutls-dev libfuse-dev libsqlite3-dev zlib1g-dev libncurses5-dev pkg-config libgmp-dev fuse

Inicializando o OPAM: Quando for solicitada a confirmação para modificar seu perfil, digite N para recusá-la.)

	$ opam init

Atualize a lista de pacotes disponíveis:

	$ opam update

Compilar e instalar o Google Drive Ocamlfuse:

	$ opam install google-drive-ocamlfuse

Edite seu arquivo .bashrc para adicionar o caminho ( PATH="$PATH:$HOME/.opam/default/bin" ) na 4 linha para o software opam ser executável, Você pode abrir seu .bashrc para edição usando o nano conforme mostrado a seguir:

	$ nano ~/.bashrc (adicionar PATH="$PATH:$HOME/.opam/default/bin" na 4 linha)

Em seguida, execute o seguinte comando:

	$ source ~/.bashrc

Acesse o site https://console.cloud.google.com/apis/dashboard
Entre com sua conta do Google e Crie um novo projeto.
Comforme o arquivo procedimento-google.pdf na pasta /doc.

Vincule o seu google drive ao GOOGLE-DRIVE-OCAMLFUSE SUBSTITUINDO $SEU_ID_CLIENTE E $SUA_CHAVE_SECRETA Copiados do seu projeto conforme PDF segue comando a ser utilizado:

	$ google-drive-ocamlfuse -device -label cgnat -id $SEU_ID_CLIENTE -secret $SUA_CHAVE_SECRETA
	
Aparecerá o seguinte no seu terminal:

	Please, open the following URL in a web browser: https://www.google.com/device
	and enter the following code: xxxx-xxxx

Copie o "https://www.google.com/device" e cole no navegador do seu computador.
ele ira pedir para inserir o codigo, copie o codigo "xxxx-xxxx" e cole no navegador.
ele ira pedir para permitir, você confirma.

Após isso no terminal do seu linux aparecerá o seguinte:

	Access token retrieved correctly.
	
Edite o fuse.conf e descomentar a linha com user_allow_other:

	$ sudo nano /etc/fuse.conf

Salve e feche o arquivo.

Crie a pasata para nossos arquivos:

	$ sudo mkdir /var/log/cgnat

Agora inicie o google-drive-ocamlfuse:

	$ google-drive-ocamlfuse -o allow_other -label cgnat /var/log/cgnat

AUTOMATIZAR INICIALIZAÇÃO DO GOOGLE-DRIVE-OCAMLFUSE
========================================================================
Crie o seguinte arquivo shell script:

	$ sudo nano /usr/bin/gdfuse 

coloque o seguinte conteudo no arquivo substituindo $USERNAME pelo seu usuario

	#!/bin/bash
	su $USERNAME -l -c "google-drive-ocamlfuse -label $1 $*"
	exit 0

Salve e feche o arquivo.

Dê premissão de execução:

	$ sudo chmod +x /usr/bin/gdfuse

Edite o arquivo /etc/fstab:

	$ sudo nano /etc/fstab

Adicione a seguinte linha no final do seu arquivo:

	gdfuse#cgnat  /var/log/cgnat     fuse    x-systemd.after=network-online.target,uid=0,gid=0,allow_other,user,exec,rw     0       0

reinicie o sistema e verifique o tamanho da pasta /var/log/cgnat se está com o tamanho do seu google drive:

	$ sudo df -h

INSTALAÇÂO SYSLOG-NG
========================================================================
Vamos edita e deixar o arquivo /etc/apt/sources.list da seguinte forma: 

	deb http://security.debian.org/debian-security bullseye-security main contrib non-free
	deb http://deb.debian.org/debian bullseye main non-free contrib
	deb http://deb.debian.org/debian bullseye-updates main contrib non-free
	deb http://deb.debian.org/debian bullseye-backports main contrib non-free

Entrando no modo root:

	$ su -

Instalando e iniciando alguns pacotes:

	# apt install net-tools htop iotop sipcalc tcpdump curl gnupg rsync wget host dnsutils mtr-tiny bmon sudo tmux whois syslog-ng nfdump pigz chrony irqbalance
	# systemctl enable irqbalance
	# echo "vm.swappiness=10" >> /etc/sysctl.conf
	# sysctl -p

Altere o fuso horário do servidor para UTC com o seguinte comando:

	# nano  /etc/chrony/chrony.conf

Comente a linha com "pool 2.debian.pool.ntp.org iburst" e na sequência adicione as linhas conforme abaixo:

	server a.st1.ntp.br iburst nts
	server b.st1.ntp.br iburst nts
	server c.st1.ntp.br iburst nts
	server d.st1.ntp.br iburst nts

Salve e feche o arquivo.

Reinicie o serviço chronyd:

	# systemctl restart chronyd.service

Configure o tzdata para horário UTC:

	# timedatectl set-timezone UTC

Configurando o Syslog
========================================================================
criar pasta dentro do diretorio principal:

	# mkdir -p /var/log/cgnat/syslog

A estrutura de armazenamento utilizada para o syslog neste é esta abaixo:

	/var/log/cgnat/syslog/<HOSTNAME>/<ANO>/<MES>/<DIA>/server-<HORA>.log 

Para configurarmos essa estrutura vamos alterar primeiramente o arquivo: 

	# nano /etc/syslog-ng/syslog-ng.conf

E alterar o options para esse abaixo:

	options { chain_hostnames(off); flush_lines(0); use_dns(no); use_fqdn(no); keep_hostname (yes);
         	 dns_cache(no); owner("root"); group("root"); perm(0644); dir_perm(0700); create_dirs (yes);
          	stats_freq(0); bad_hostname("^gconfd$"); keep-timestamp(off);
	};

Salve e feche o arquivo.

Vamos criar um arquivo de configuração que vai ser o responsável por receber e criar a estrutura acima.
Crie o seguinte novo arquivo:

	# nano /etc/syslog-ng/conf.d/isp.conf 

Com o conteúdo de exemplo abaixo para IPv6 SUBSTITUINDO $IPv6-DO-SERVIDOR pelo IPv6 do seu servidor de log:

	source s_net {
		udp6(ip("$IPv6-DO-SERVIDOR") port(514));
	};

	destination d_ce { file("/var/log/cgnat/syslog/${HOST}/${YEAR}/${MONTH}/${DAY}/server-${HOUR}.log"); };

	filter f_ce { facility(daemon) and not message(".*SSH.*"); };
	filter f_ce_ipv6 { facility(syslog); };

	log { source(s_net); filter(f_ce); destination(d_ce); };
	log { source(s_net); filter(f_ce_ipv6); destination(d_ce); };

Ou o conteúdo de exemplo abaixo para IPv4 SUBSTITUINDO $IPv4-DO-SERVIDOR pelo IPv4 do seu servidor de log:

	source s_net {
	   udp(ip("$IPv4-DO-SERVIDOR") port(514));
	};

	destination d_ce { file("/var/log/cgnat/syslog/${HOST}/${YEAR}/${MONTH}/${DAY}/server-${HOUR}.log"); };

	filter f_ce { facility(daemon) and not message(".*SSH.*"); };
	filter f_ce_ipv6 { facility(syslog); };

	log { source(s_net); filter(f_ce); destination(d_ce); };
	log { source(s_net); filter(f_ce_ipv6); destination(d_ce); };

Salve e feche o arquivo.

Reiniciamos o serviço :

	# systemctl restart syslog-ng.service

Compactando os logs de syslog diariamente
==========================================================
Crie a pasta:

	# mkdir -p /root/scripts

Crie o seguinte arquivo:

	# nano /root/scripts/compacta_syslog.sh

Com o conteudo abaixo:

	#!/bin/bash 
	ANO=$(date -d "-1 day" '+%Y')
	MES=$(date -d "-1 day" '+%m')
	DIA=$(date -d "-1 day" '+%d')
	for lista in /var/log/cgnat/syslog/*; do
	   if [ -d $lista/$ANO/$MES/$DIA ]; then
	        pigz -p4 --fast $lista/$ANO/$MES/$DIA/*
	   fi
	done

Salve e feche o arquivo.

Dê permisão de excução:

	# chmod 700 /root/scripts/compacta_syslog.sh

Adicione ao crontab com o seguinte comando:

	# echo "05 0    * * *   root    /root/scripts/compacta_syslog.sh" >> /etc/crontab

Configurando o Netflow
========================================================================
Crie a pasta com o HOSTNAME do equipamento monitorado:

	# mkdir -p /var/log/cgnat/flow/HOSTNAME

Edite a inicialização do nfdump:

	# nano /etc/nfdump/default.conf

Comente a linha que inicia com "options=" e na sequência adicione as linhas conforme abaixo:
(substituindo $HOSTNAME pelo hostname do equipamento monitorado, $IP-DO-EQUIPAMENTO pelo ip do equipamento monitorado, $IP-DO-SERVIDOR pelo ip do seu servidor de log esse comando vale para IPv6 ou IPv4)

	options='-D -w -T all -t 3600 -S 1 -B 200000 -z -n $HOSTNAME,$IP-DO-EQUIPAMENTO,/var/log/cgnat/flow/$HOSTNAME -b $IP-DO-SERVIDOR -p 2055'

Salve e feche o arquivo.

Pare o nfdump.service:

	# systemctl stop nfdump

Verifique se esta inativo:

	# systemctl status nfdump 

Desabilite do boot automatico:

	# systemctl disable nfdump

Remova o nfdump@.service:

	# rm /lib/systemd/system/nfdump@.service

Crie o novo service para iniciar no boot do linux:

	# nano /lib/systemd/system/nfdump-cgnat.service

Com o conteudo abaixo:

	[Unit]
	Description=netflow capture daemon, logcgnat instance
	Documentation=man:nfcapd(1)
	After=network.target var-log-cgnat.mount 

	[Service]
	Type=forking
	EnvironmentFile=/etc/nfdump/default.conf
	ExecStart=/usr/bin/nfcapd -D -P /run/nfcapd.default.pid $options
	PIDFile=/run/nfcapd.default.pid
	KillMode=process
	Restart=no

	[Install]
	WantedBy=multi-user.target

Salve e feche o arquivo.

Ative o serviço no boot:

	# systemctl enable nfdump-cgnat

Compactando os logs de netflow diariamente
==========================================================
Crie o seguinte arquivo:

	# nano /root/scripts/compacta_flow.sh

Com o conteudo abaixo:

	#!/bin/bash
	ANO=$(date -d "-1 day" '+%Y')
	MES=$(date -d "-1 day" '+%m')
	DIA=$(date -d "-1 day" '+%d')
	for FOLDER in /var/log/cgnat/flow/*; do
	  if [ -d $FOLDER/$ANO/$MES/$DIA ]; then
	     cd $FOLDER/$ANO/$MES/$DIA
	     echo "Compactando: ${FOLDER}/$ANO/$MES/$DIA/"
	     pigz -p4 --fast nfcapd*
	  fi
	done

Salve e feche o arquivo.

Dê permisão de excução:

	# chmod 700 /root/scripts/compacta_flow.sh

Adicione ao crontab:

	# echo "05 0    * * *   root    /root/scripts/compacta_flow.sh" >> /etc/crontab

Reinicie serviço e verificar se adicionou as regra automaticamente:

	# reboot
	$ sudo netstat -tulpn

Instalação Apache
==========================================================================
Entrando no modo root:

	$ su -

Atualizando os pacotes com:

	# apt update
	# apt upgrade

Instalando os pacotes com:

	# apt install apache2 apache2-utils

Habilite o mod_rewrite e o mod_headers do Apache

	# a2enmod rewrite
	# a2enmod headers

Abra o arquivo abaixo :

	# nano /etc/apache2/sites-enabled/000-default.conf

E adicione abaixo de *DocumentRoot /var/www/html* o seguinte:
	
	Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains"
 
	<Directory /var/www/html/>
    		Options FollowSymLinks
    		AllowOverride All
	</Directory>
	
Salve e feche o arquivo.

Por segurança remova a assinatura do apache e reinicie o apache2 para que tenha efeito as nossas alterações com o comando abaixo:

	# sed -i 's/ServerTokens OS/ServerTokens Prod/' /etc/apache2/conf-available/security.conf
	# sed -i 's/ServerSignature On/ServerSignature Off/' /etc/apache2/conf-available/security.conf
	# systemctl restart apache2

instalação MariaDB
===========================================================================
Instalando os pacotes com:

	# apt install mariadb-server mariadb-client 

instalação PHP 7.4
===========================================================================
Instalando os pacotes com:

	# apt install libapache2-mod-php php php-mysql php-cli php-pear php-gmp php-gd php-bcmath php-mbstring php-curl php-xml php-zip

Reinicie o apache para que o php tenha efeito:

	# systemctl restart apache2

Consulte a informações de versão:

	# php --version

Instalar nossa Interface Web
=======================================================================
Entre na pasta do site:

	# cd /var/www/html

Verifique se exite algum arquivo na pasta:

	# ls

Provavelmente tem o index.html vamos deleta-lo e se houver mais algum delete tambem:

	# rm index.html

Volte para pasta www:

	# cd ../

Baixe o arquivo da interface web:

	# wget https://flowspec.net.br/logcgnat/cgnat.tar.gz

Descompacte o arquivo:

	# tar -xzvf cgnat.tar.gz

Delete o arquivo compactado:

	# rm cgnat.tar.gz
 
============================Configurando banco de Dados======================================

Acesse o banco de dados conforme a seguir por padrão o maria db vem sem senha é só clicar em enter quando pedir senha:

	# mariadb -u root -p

Crie um DB, usuario e senha para nossa interface web se conectar lembre-se de coloca a sua senha:

	> CREATE DATABASE LOGCGNAT;
	> CREATE USER 'logcgnat'@'localhost' IDENTIFIED BY 'SENHA SEGURA';
	> GRANT ALL PRIVILEGES ON LOGCGNAT.* TO 'logcgnat'@'localhost' IDENTIFIED BY 'SENHA SEGURA' WITH GRANT OPTION;
	> FLUSH PRIVILEGES;
	> EXIT;

Importe as Tabelas:
	
	# mariadb -u root -p LOGCGNAT < /var/www/html/conf/sql/logcgnat.sql

Proteja o root do mysql com uma senha:
	
	# mariadb -u root -p

	> USE mysql;
	> ALTER USER 'root'@'localhost' IDENTIFIED BY 'SUA SENHA ROOT SEGURA';
	> FLUSH PRIVILEGES;
	> EXIT;


Limpe o hystorico do mysql por segurança:

	# echo > /root/.mysql_history

Configurando nossa Interface Web
================================================================
Abra o arquivo abaixo :

	# nano /var/www/html/conf/config.php

Altere a seguinte linha e coloque a senha configurada para o usuario 'logcgnat' no banco de dados: 

	define('DB_PASSWORD', 'SUA SENHA');

Salve e feche o arquivo.

Agora acesse:
	
	http://IP.DO.SEU.SERVIDOR/
	Usuario: Admin
	Senha: admin123
