FROM nginx:alpine

# Копирование конфигурации
COPY conf.d/default.conf /etc/nginx/conf.d/default.conf

# Создание директории для логов
RUN mkdir -p /var/log/nginx

# Expose порты
EXPOSE 80 443

CMD ["nginx", "-g", "daemon off;"]
