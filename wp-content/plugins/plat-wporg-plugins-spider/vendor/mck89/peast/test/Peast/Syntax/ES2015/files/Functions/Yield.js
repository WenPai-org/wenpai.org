function *a(){
    yield
}

function *b(){
    yield index++;
}

function *c(){
    yield *d;
}