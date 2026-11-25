const API_URL = '../back/api.php';

async function apiRequest(rota, metodo = 'GET', dados = null, parametros = {}) {
    const query = new URLSearchParams({ rota, ...parametros });

    const opcoes = {
        method: metodo,
        headers: {
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    };

    if (dados !== null) {
        opcoes.headers['Content-Type'] = 'application/json';
        opcoes.body = JSON.stringify(dados);
    }

    try {
        const resposta = await fetch(`${API_URL}?${query}`, opcoes);
        const texto = await resposta.text();

        let resultado;

        try {
            resultado = JSON.parse(texto);
        } catch {
            throw new Error('Resposta inválida do PHP: ' + texto);
        }

        if (!resposta.ok) {
            throw new Error(resultado.erro || 'Erro na solicitação.');
        }

        return resultado;

    } catch (erro) {
        console.error('Erro na API:', erro);
        throw erro;
    }
}

async function obterUsuarioLogado() {
    const resultado = await apiRequest(
        'usuario',
        'GET',
        null,
        { acao: 'me' }
    );

    return resultado.usuario;
}
