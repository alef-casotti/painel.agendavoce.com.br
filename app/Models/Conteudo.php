<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conteudo extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descricao',
        'criador',
        'plataforma',
        'tipo_conteudo',
        'modelo',
        'slides',
        'html_gerado',
        'data_publicacao',
        'horario',
        'status',
        'link',
        'user_id',
    ];

    protected $casts = [
        'data_publicacao' => 'date',
        'slides' => 'array',
    ];

    public const STATUS_RASCUNHO = 'rascunho';
    public const STATUS_AGENDADO = 'agendado';
    public const STATUS_PUBLICADO = 'publicado';

    public const TIPO_CARROSSEL = 'carrossel';

    public const MODELO_ORGANIZACAO = 'organizacao';
    public const MODELO_MARKETING = 'marketing';
    public const MODELO_DINHEIRO = 'dinheiro';
    public const MODELO_CLIENTE = 'cliente';
    public const MODELO_ERROS_COMUNS = 'erros_comuns';
    public const MODELO_HISTORIA = 'historia';
    public const MODELO_BASTIDOR = 'bastidor';
    public const MODELO_REFLEXAO = 'reflexao';
    public const MODELO_CURIOSIDADE = 'curiosidade';

    public static function plataformas(): array
    {
        return [
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'youtube' => 'YouTube',
            'linkedin' => 'LinkedIn',
            'blog' => 'Blog',
            'outro' => 'Outro',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_RASCUNHO => 'Rascunho',
            self::STATUS_AGENDADO => 'Agendado',
            self::STATUS_PUBLICADO => 'Publicado',
        ];
    }

    public static function tipos(): array
    {
        return [
            self::TIPO_CARROSSEL => 'Carrossel',
        ];
    }

    public static function modelosCarrossel(): array
    {
        return [
            self::MODELO_ORGANIZACAO => [
                'label' => 'Organização',
                'descricao' => 'Rotina, agenda e produtividade do negócio',
                'estrutura' => 'Capa → N práticas → CTA',
            ],
            self::MODELO_MARKETING => [
                'label' => 'Marketing',
                'descricao' => 'Divulgação, atração e presença digital',
                'estrutura' => 'Capa → N estratégias → CTA',
            ],
            self::MODELO_DINHEIRO => [
                'label' => 'Dinheiro',
                'descricao' => 'Faturamento, precificação e crescimento',
                'estrutura' => 'Capa → N insights → CTA',
            ],
            self::MODELO_CLIENTE => [
                'label' => 'Cliente',
                'descricao' => 'Experiência, retenção e relacionamento',
                'estrutura' => 'Capa → N ações → CTA',
            ],
            self::MODELO_ERROS_COMUNS => [
                'label' => 'Erros comuns',
                'descricao' => 'O que evitar na gestão do negócio',
                'estrutura' => 'Capa → N erros → Correção/CTA',
            ],
            self::MODELO_HISTORIA => [
                'label' => 'História',
                'descricao' => 'Narrativa pessoal ou do negócio',
                'estrutura' => 'Abertura → Contexto → Virada → Lição',
            ],
            self::MODELO_BASTIDOR => [
                'label' => 'Bastidor',
                'descricao' => 'Como as coisas acontecem nos bastidores',
                'estrutura' => 'Capa → N cenas → Fechamento',
            ],
            self::MODELO_REFLEXAO => [
                'label' => 'Reflexão',
                'descricao' => 'Pensamentos que geram conexão e autoridade',
                'estrutura' => 'Gancho → Desenvolvimento → Fechamento',
            ],
            self::MODELO_CURIOSIDADE => [
                'label' => 'Curiosidade',
                'descricao' => 'Fatos, dados e insights surpreendentes',
                'estrutura' => 'Gancho → N curiosidades → CTA',
            ],
        ];
    }

    /**
     * Slides iniciais sugeridos por modelo (páginas variáveis depois).
     */
    public static function slidesPadrao(string $modelo): array
    {
        return match ($modelo) {
            self::MODELO_ORGANIZACAO => [
                ['titulo' => 'Sua agenda merece mais ordem', 'texto' => 'Pequenos hábitos mudam o dia a dia do seu negócio.', 'destaque' => 'Organização'],
                ['titulo' => 'Bloqueie pausas', 'texto' => 'Reserve intervalos reais entre atendimentos para não se sobrecarregar.', 'destaque' => '1'],
                ['titulo' => 'Padronize horários', 'texto' => 'Defina janelas fixas de atendimento e reduza o improvisos.', 'destaque' => '2'],
                ['titulo' => 'Centralize tudo', 'texto' => 'Clientes, serviços e horários em um só lugar evitam confusão.', 'destaque' => '3'],
                ['titulo' => 'Organize com o Agenda Você', 'texto' => 'Menos planilha, mais tempo para atender bem.', 'destaque' => 'CTA'],
            ],
            self::MODELO_MARKETING => [
                ['titulo' => 'Marketing que agenda clientes', 'texto' => 'Não basta aparecer: precisa facilitar o próximo passo.', 'destaque' => 'Marketing'],
                ['titulo' => 'Mostre sua disponibilidade', 'texto' => 'Publique seu link de agenda e deixe o cliente marcar sozinho.', 'destaque' => '1'],
                ['titulo' => 'Conteúdo + conversão', 'texto' => 'Cada post pode terminar com um convite claro para agendar.', 'destaque' => '2'],
                ['titulo' => 'Consistência vence', 'texto' => 'Aparecer com frequência gera confiança e demanda.', 'destaque' => '3'],
                ['titulo' => 'Transforme audiência em agenda', 'texto' => 'Use o Agenda Você como ponte entre o conteúdo e o atendimento.', 'destaque' => 'CTA'],
            ],
            self::MODELO_DINHEIRO => [
                ['titulo' => 'Agenda cheia também é faturamento', 'texto' => 'Horários ociosos e faltas drenam o resultado do mês.', 'destaque' => 'Dinheiro'],
                ['titulo' => 'Reduza no-shows', 'texto' => 'Lembretes e confirmação protegem a receita do dia.', 'destaque' => '1'],
                ['titulo' => 'Preencha buracos na agenda', 'texto' => 'Enxergue horários livres e ofereça encaixes com estratégia.', 'destaque' => '2'],
                ['titulo' => 'Valorize seu tempo', 'texto' => 'Organização libera espaço para serviços de maior valor.', 'destaque' => '3'],
                ['titulo' => 'Cuide da receita com organização', 'texto' => 'Comece a estruturar sua agenda no Agenda Você.', 'destaque' => 'CTA'],
            ],
            self::MODELO_CLIENTE => [
                ['titulo' => 'Cliente feliz volta (e indica)', 'texto' => 'A experiência começa antes do atendimento.', 'destaque' => 'Cliente'],
                ['titulo' => 'Facilite o agendamento', 'texto' => 'Quanto mais simples marcar, maior a chance de converter.', 'destaque' => '1'],
                ['titulo' => 'Comunique com clareza', 'texto' => 'Confirmações e lembretes reduzem ansiedade e faltas.', 'destaque' => '2'],
                ['titulo' => 'Respeite o tempo dele', 'texto' => 'Pontualidade e organização geram confiança imediata.', 'destaque' => '3'],
                ['titulo' => 'Melhore a jornada do cliente', 'texto' => 'Do primeiro clique ao atendimento, com o Agenda Você.', 'destaque' => 'CTA'],
            ],
            self::MODELO_ERROS_COMUNS => [
                ['titulo' => 'Erros que esvaziam sua agenda', 'texto' => 'Identifique o que está sabotando sua rotina.', 'destaque' => 'Erros'],
                ['titulo' => 'Erro: marcar só no WhatsApp', 'texto' => 'Mensagens se perdem e horários se sobrepõem.', 'destaque' => '1'],
                ['titulo' => 'Erro: não confirmar', 'texto' => 'Sem lembrete, a taxa de falta sobe sem você perceber.', 'destaque' => '2'],
                ['titulo' => 'Erro: agenda sem padrão', 'texto' => 'Horários aleatórios cansam você e confundem o cliente.', 'destaque' => '3'],
                ['titulo' => 'Corrija com um sistema claro', 'texto' => 'Organize marcações e lembretes no Agenda Você.', 'destaque' => 'CTA'],
            ],
            self::MODELO_HISTORIA => [
                ['titulo' => 'A história por trás da minha agenda', 'texto' => 'Nem sempre foi organizado — e isso tem um motivo.', 'destaque' => 'História'],
                ['titulo' => 'O caos do começo', 'texto' => 'Anotações soltas, mensagens perdidas e horários esquecidos.', 'destaque' => 'Antes'],
                ['titulo' => 'O ponto de virada', 'texto' => 'Percebi que crescer exigia um processo, não só esforço.', 'destaque' => 'Virada'],
                ['titulo' => 'O que mudou', 'texto' => 'Com rotina clara, sobrou tempo e tranquilidade para atender melhor.', 'destaque' => 'Depois'],
                ['titulo' => 'Sua história pode mudar também', 'texto' => 'Comece a organizar sua agenda com o Agenda Você.', 'destaque' => 'CTA'],
            ],
            self::MODELO_BASTIDOR => [
                ['titulo' => 'Bastidores da minha semana', 'texto' => 'O que o cliente não vê, mas faz diferença no atendimento.', 'destaque' => 'Bastidor'],
                ['titulo' => 'Planejamento do dia', 'texto' => 'Antes de abrir a agenda, reviso serviços e disponibilidade.', 'destaque' => '1'],
                ['titulo' => 'Confirmações', 'texto' => 'Os lembretes rodam enquanto eu foco no atendimento.', 'destaque' => '2'],
                ['titulo' => 'Ajustes rápidos', 'texto' => 'Encaixes e imprevistos ficam controlados no sistema.', 'destaque' => '3'],
                ['titulo' => 'Organização também é bastidor', 'texto' => 'Mostre profissionalismo com o Agenda Você.', 'destaque' => 'CTA'],
            ],
            self::MODELO_REFLEXAO => [
                ['titulo' => 'Tempo é o ativo mais caro', 'texto' => 'Quem não organiza a agenda, deixa o acaso decidir o dia.', 'destaque' => 'Reflexão'],
                ['titulo' => 'Atender mais não é atender melhor', 'texto' => 'Sem estrutura, volume vira desgaste — não crescimento.', 'destaque' => 'Pense'],
                ['titulo' => 'Clareza gera presença', 'texto' => 'Quando a rotina está clara, você aparece inteiro para o cliente.', 'destaque' => 'Pense'],
                ['titulo' => 'Organize para expandir', 'texto' => 'Liberdade profissional começa com compromissos bem definidos.', 'destaque' => 'CTA'],
            ],
            self::MODELO_CURIOSIDADE => [
                ['titulo' => 'Você sabia?', 'texto' => 'Pequenos detalhes da agenda mudam o resultado do mês.', 'destaque' => 'Curiosidade'],
                ['titulo' => 'Faltas custam caro', 'texto' => 'Um no-show não é só um horário vazio — é receita perdida.', 'destaque' => '1'],
                ['titulo' => 'Clientes preferem autonomia', 'texto' => 'Muitos querem marcar sozinhos, no horário que for melhor para eles.', 'destaque' => '2'],
                ['titulo' => 'Lembretes aumentam comparecimento', 'texto' => 'Confirmação prévia reduz esquecimento e melhora a taxa de presença.', 'destaque' => '3'],
                ['titulo' => 'Transforme curiosidade em ação', 'texto' => 'Teste uma agenda mais inteligente com o Agenda Você.', 'destaque' => 'CTA'],
            ],
            default => [
                ['titulo' => 'Título da página', 'texto' => 'Texto da página', 'destaque' => ''],
            ],
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPlataformaLabelAttribute(): string
    {
        return self::plataformas()[$this->plataforma] ?? $this->plataforma;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getTipoConteudoLabelAttribute(): string
    {
        return self::tipos()[$this->tipo_conteudo] ?? $this->tipo_conteudo ?? '';
    }

    public function getModeloLabelAttribute(): string
    {
        if (! $this->modelo) {
            return '';
        }

        return self::modelosCarrossel()[$this->modelo]['label'] ?? $this->modelo;
    }
}
