#!/bin/bash

declare -A array_name

array_name[ishemia.loc]=10.5.0.5
array_name[www.ishemia.loc]=10.5.0.5
array_name[pg-ishemia]=10.5.0.6
array_name[www.pg-ishemia.loc]=10.5.0.6

for i in "${!array_name[@]}"; do

  if sudo grep $i /etc/hosts; then
    echo "Хост "$i" уже настроен"
  else
    sudo -- sh -c "echo "${array_name[$i]}"  "$i" >> /etc/hosts"

    echo "Хост "$i" добавлен"
  fi

done
