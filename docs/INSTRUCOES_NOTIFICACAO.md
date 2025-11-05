# Instruções - Sistema de Notificação Sonora

## Arquivo de Áudio Necessário

Para que o sistema de notificação sonora funcione, você precisa adicionar um arquivo MP3 na seguinte localização:

**Caminho:** `public/sounds/notification.mp3`

### Como adicionar:

1. Crie a pasta `sounds` dentro de `public/` (se não existir):
   ```
   public/sounds/
   ```

2. Coloque um arquivo MP3 de notificação chamado `notification.mp3` dentro dessa pasta.

3. O arquivo será reproduzido automaticamente quando:
   - Um novo ticket for criado
   - Uma nova mensagem de cliente chegar
   - O som continuará tocando em loop até que todas as mensagens sejam visualizadas

### Recomendações:

- Use um arquivo MP3 curto (2-5 segundos)
- Volume moderado
- Formato: MP3
- Nome do arquivo: `notification.mp3` (exatamente)

### Exemplos de sons gratuitos:

Você pode baixar sons de notificação gratuitos em sites como:
- freesound.org
- zapsplat.com
- notification-sounds.com
