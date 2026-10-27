test('unauthenticated user visiting root redirects to login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
