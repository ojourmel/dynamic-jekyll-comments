# Extending the Post class.
# See https://github.com/mojombo/jekyll/blob/master/lib/jekyll/post.rb
module Jekyll
  class Post
 
    # Rewrite to allow posts with .php extensions.
    # Do this for all posts with comments enabled.
    def destination(dest)
      # The url needs to be unescaped in order to preserve the correct filename
      path = File.join(dest, CGI.unescape(self.url))
      fn = (self.data['comments']) ? 'index.php' : 'index.html'
      path = File.join(path, fn) if template[/\.html$/].nil?
      path
    end
 
  end
end
