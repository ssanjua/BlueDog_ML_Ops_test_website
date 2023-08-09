# Usar imagen oficial de PHP con Apache
FROM php:8.2.8-apache

# Instalar extensiones PHP necesarias y habilitarlas
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copiar los archivos del frontend al directorio público de Apache
COPY frontend/ /var/www/html/

# Instalar Python y pip
RUN apt-get update && apt-get install -y python3.9 python3-pip

# Copiar backend y archivos de requisitos a un directorio temporal
COPY backend/ /tmp/backend/
COPY requirements.txt /tmp/

# Instalar las bibliotecas de Python necesarias
RUN pip3 install --no-cache-dir -r /tmp/requirements.txt

# Exponer el puerto 80 para Apache
EXPOSE 80

# Establecer el directorio de trabajo en /var/www/html
WORKDIR /var/www/html

# Comando para mantener el contenedor en ejecución
CMD ["apache2-foreground"]