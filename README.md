# logcgnat
Sistema de verificação de log cgnat no google drive
=======================================================
Este web sistema tem como objetivo a realização de verifcação de log cgnat.
atraves dos sistema sylog-ng https://www.syslog-ng.com/
com amarzenamento na nuvem do google drive através do https://github.com/astrada/google-drive-ocamlfuse
=========================================================================================================
    INSTALAÇÃO LIMPA DO DEBIAN 11. 
    NÃO UTILIZAR ROOT PARA INSTALAR O GOOGLE-DRIVE. 
========================================INSTALAÇÂO GOOGLE-DRIVE-OCAMLFUSE================================
instalando e iniciando alguns pacotes:
$ sudo apt update
$ sudo apt install opam -y
$ sudo apt install m4 libcurl4-gnutls-dev libfuse-dev libsqlite3-dev zlib1g-dev libncurses5-dev pkg-config libgmp-dev fuse -y

Inicializando o OPAM:
$ opam init (Quando for solicitada a confirmação para modificar seu perfil, digite N para recusá-la.)

Atualize a lista de pacotes disponíveis:
$ opam update

Compilar e instalar o Google Drive Ocamlfuse:
$ opam install google-drive-ocamlfuse

Edite seu arquivo .bashrc para adicionar o caminho para o software opam ser executável. 
Você pode abrir seu .bashrc para edição usando o nano conforme mostrado a seguir:
$ nano ~/.bashrc (adicionar PATH="$PATH:$HOME/.opam/default/bin" na 4 linha)

Em seguida, execute o seguinte comando:
$ source ~/.bashrc

acesse o site https://console.cloud.google.com/apis/dashboard
Entre com sua conta do Google e Crie um novo projeto seguindo as imagens a seguir:
![imagem1](https://github.com/Robsonvbt/logcgnat/assets/101009949/b4de679f-c102-4265-b314-d025b5c42a76)

