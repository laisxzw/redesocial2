const API_URL = '../back/api.php';

async function apiRequest(rota, metodo = 'GET', dados = null, parametros = {}) {
    const query = new URLSearchParams({ rota, ...parametros });
    const opcoes = {
        method: metodo,
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin'
    };

    if (dados !== null) {
        opcoes.headers['Content-Type'] = 'application/json';
        opcoes.body = JSON.stringify(dados);
    }

    let resposta;
    try {
        resposta = await fetch(`${API_URL}?${query.toString()}`, opcoes);
    } catch (erro) {
        throw new Error('Não consegui acessar o PHP. Abra o projeto pelo servidor PHP em http://localhost:8000.');
    }

    const texto = await resposta.text();
    let resultado;
    try {
        resultado = texto ? JSON.parse(texto) : {};
    } catch {
        throw new Error('O PHP não retornou JSON válido. Confira os erros no back-end.');
    }

    if (!resposta.ok) {
        throw new Error(resultado.erro || 'Ocorreu um erro na solicitação.');
    }
    return resultado;
}

async function obterUsuarioLogado() {
    const resultado = await apiRequest('usuario', 'GET', null, { acao: 'me' });
    return resultado.usuario;
}
