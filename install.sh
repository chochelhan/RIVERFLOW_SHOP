#!/bin/bash



# Composer가 설치되어 있는지 확인
if ! [ -x "$(command -v composer)" ]; then
  echo "Composer가 설치되어 있지 않습니다. 설치를 진행합니다..."
  
  # Composer 설치
  EXPECTED_SIGNATURE="$(wget -q -O - https://composer.github.io/installer.sig)"
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  ACTUAL_SIGNATURE="$(php -r "echo hash_file('SHA384', 'composer-setup.php');")"

  if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]
  then
      >&2 echo 'ERROR: Invalid installer signature'
      rm composer-setup.php
      exit 1
  fi

  php composer-setup.php --quiet
  RESULT=$?
  rm composer-setup.php

  if [ $RESULT -eq 0 ]; then
    echo "success install Composer."
    mv composer.phar /usr/local/bin/composer
  else
    echo "fail install Composer"
    exit 1
  fi
fi

# composer install 실행
composer install

# 설치 결과 출력
if [ $? -eq 0 ]; then
  echo "Composer install complete."
else
  echo "Composer install fail."
  exit 1
fi



# install
install_dir="./install"
find "$install_dir" -type d -exec chmod 777 {} \;

#public
public_dir="./public"
find "$public_dir" -type d -exec chmod 777 {} \;


# bootstrap cache
bootstrap_cache_dir="./bootstrap/cache"
find "$bootstrap_cache_dir" -type d -exec chmod 777 {} \;


# storage app public
storage_public_dir="./storage/app/public"
find "$storage_public_dir" -type d -exec chmod  777 {} \;


# storage framework
storage_framework_dir="./storage/framework"
find "$storage_framework_dir" -type d -exec chmod  777 {} \;


# log file
storage_log_file="./storage/logs/laravel.log"
find "$storage_log_file" -type f -exec chmod 777 {} \;


# mail
resources_mail_dir="./resources/views/mail"
find "$resources_mail_dir" -type d -exec chmod  777 {} \;



echo "complete!"
